<?php

namespace App\Services\Commande\Implementations;

use App\DTO\Commande\CommandeDTO;
use App\Models\Commande;
use App\Repositories\Commande\Interfaces\CommandeRepositoryInterface;
use App\Services\Commande\Interfaces\CommandeServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CommandeService implements CommandeServiceInterface
{
    private const MAX_QUANTITY = 4_294_967_295;

    private const MAX_ORDER_TOTAL_CENTIMES = 999_999_999_999;

    public function __construct(
        private readonly CommandeRepositoryInterface $commandeRepository
    ) {}

    /**
     * Créer une nouvelle commande.
     */
    public function create(CommandeDTO $dto): Commande
    {
        return DB::transaction(function () use ($dto) {
            if ($dto->details === []) {
                throw ValidationException::withMessages([
                    'details' => 'Une commande doit contenir au moins un produit.',
                ]);
            }

            $quantitiesByProduct = [];
            foreach ($dto->details as $detail) {
                if (! is_array($detail)) {
                    throw ValidationException::withMessages([
                        'details' => 'Le format des détails de commande est invalide.',
                    ]);
                }

                $produitId = filter_var($detail['produit_id'] ?? null, FILTER_VALIDATE_INT);
                $quantite = filter_var($detail['quantite'] ?? null, FILTER_VALIDATE_INT);

                if ($produitId === false || $produitId < 1) {
                    throw ValidationException::withMessages([
                        'details' => 'Un identifiant de produit est invalide.',
                    ]);
                }

                if ($quantite === false || $quantite < 1 || $quantite > self::MAX_QUANTITY) {
                    throw ValidationException::withMessages([
                        'details' => 'Chaque quantité doit être un entier positif dans la limite autorisée.',
                    ]);
                }

                if (isset($quantitiesByProduct[$produitId])) {
                    throw ValidationException::withMessages([
                        'details' => 'Un produit ne peut apparaître qu’une seule fois.',
                    ]);
                }

                $quantitiesByProduct[$produitId] = $quantite;
            }

            $productIds = array_keys($quantitiesByProduct);

            $products = $this->commandeRepository
                ->findProductsByIdsForUpdate($productIds);

            if ($products->count() !== count($productIds)) {
                throw ValidationException::withMessages([
                    'details' => 'Un ou plusieurs produits sont introuvables.',
                ]);
            }

            $preparedDetails = [];
            $montantTotalEnCentimes = 0;
            foreach ($products as $produit) {
                $quantite = $quantitiesByProduct[$produit->id];

                if (! $produit->is_active) {
                    throw ValidationException::withMessages([
                        'details' => "Le produit {$produit->nom} n'est pas disponible.",
                    ]);
                }

                if ($produit->stock < $quantite) {
                    throw ValidationException::withMessages([
                        'details' => "Stock insuffisant pour le produit {$produit->nom}.",
                    ]);
                }

                $prixEnCentimes = $this->toCentimes($produit->prix);
                $sousTotalEnCentimes = $prixEnCentimes * $quantite;

                if ($sousTotalEnCentimes > self::MAX_ORDER_TOTAL_CENTIMES - $montantTotalEnCentimes) {
                    throw ValidationException::withMessages([
                        'details' => 'Le montant total de la commande dépasse la limite autorisée.',
                    ]);
                }

                $montantTotalEnCentimes += $sousTotalEnCentimes;
                $preparedDetails[] = [
                    'produit_id' => $produit->id,
                    'quantite' => $quantite,
                    'prix_unitaire' => $this->formatMoney($prixEnCentimes),
                    'sous_total' => $this->formatMoney($sousTotalEnCentimes),
                ];
            }

            $commande = $this->commandeRepository->create([
                'numero_commande' => 'CMD-'.Str::uuid(),
                'user_id' => $dto->user_id,
                'montant_total' => $this->formatMoney(
                    $montantTotalEnCentimes
                ),
                'statut' => 'en_attente',
            ]);

            foreach ($preparedDetails as $detail) {

                $this->commandeRepository->createDetail([
                    'commande_id' => $commande->id,
                    'produit_id' => $detail['produit_id'],
                    'quantite' => $detail['quantite'],
                    'prix_unitaire' => $detail['prix_unitaire'],
                    'sous_total' => $detail['sous_total'],
                ]);
            }

            return $commande->load('details.produit');
        });
    }

    /**
     * Convertir un montant décimal en centimes.
     */
    private function toCentimes(string|int|float $montant): int
    {
        $montant = number_format((float) $montant, 2, '.', '');

        [$entier, $decimal] = explode('.', $montant);

        return ((int) $entier * 100) + (int) $decimal;
    }

    /**
     * Convertir les centimes en montant décimal.
     */
    private function formatMoney(int $centimes): string
    {
        return intdiv($centimes, 100)
            .'.'
            .str_pad(
                (string) ($centimes % 100),
                2,
                '0',
                STR_PAD_LEFT
            );
    }

    public function confirm(int $commandeId, ?int $userId): Commande
    {
        return DB::transaction(function () use ($commandeId, $userId) {

            // Récupérer et verrouiller la commande du propriétaire connecté.
            $commande = $this->commandeRepository
                ->findByIdForUpdate($commandeId, $userId);

            if (! $commande) {
                throw (new ModelNotFoundException)
                    ->setModel(Commande::class, [$commandeId]);
            }

            if ($commande->statut !== 'en_attente') {
                throw ValidationException::withMessages([
                    'commande' => 'Cette commande ne peut pas être confirmée.',
                ]);
            }

            $commande->load('details');

            if ($commande->details->isEmpty()) {
                throw ValidationException::withMessages([
                    'commande' => 'Cette commande ne contient aucun produit.',
                ]);
            }

            $productIds = $commande->details
                ->pluck('produit_id')
                ->unique()
                ->values()
                ->all();

            $products = $this->commandeRepository
                ->findProductsByIdsForUpdate($productIds);

            if ($products->count() !== count($productIds)) {
                throw ValidationException::withMessages([
                    'commande' => 'Un ou plusieurs produits sont introuvables.',
                ]);
            }

            $productsById = $products->keyBy('id');

            $quantitiesByProduct = $commande->details
                ->groupBy('produit_id')
                ->map(fn ($details): int => (int) $details->sum('quantite'));

            foreach ($quantitiesByProduct as $produitId => $quantite) {
                $produit = $productsById->get($produitId);

                if (! $produit || ! $produit->is_active) {
                    throw ValidationException::withMessages([
                        'stock' => 'Un produit de cette commande n’est plus disponible.',
                    ]);
                }

                if ($produit->stock < $quantite) {
                    throw ValidationException::withMessages([
                        'stock' => "Stock insuffisant pour le produit {$produit->nom}.",
                    ]);
                }
            }

            foreach ($quantitiesByProduct as $produitId => $quantite) {
                $produit = $productsById->get($produitId);
                $this->commandeRepository->updateStock(
                    $produit,
                    $quantite
                );
            }

            $commande = $this->commandeRepository->updateStatus(
                $commande,
                'confirmee'
            );

            return $commande->load('details.produit');
        });
    }

    public function paginate(?int $userId = null): LengthAwarePaginator
    {
        return $this->commandeRepository->paginate($userId);
    }

    /**
     * Consulter une commande précise.
     */
    public function show(int $commandeId, ?int $userId = null): Commande
    {
        $commande = $this->commandeRepository->findById($commandeId, $userId);

        if (! $commande) {
            throw (new ModelNotFoundException)
                ->setModel(Commande::class, [$commandeId]);
        }

        return $commande;
    }
}

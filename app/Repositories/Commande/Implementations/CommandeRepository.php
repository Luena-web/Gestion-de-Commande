<?php

namespace App\Repositories\Commande\Implementations;

use App\Models\Commande;
use App\Models\CommandeDetail;
use App\Models\Produit;
use App\Repositories\Commande\Interfaces\CommandeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CommandeRepository implements CommandeRepositoryInterface
{
    public function findProductsByIdsForUpdate(array $productIds): Collection
    {
        return Produit::query()
            ->whereIn('id', $productIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    public function findByIdForUpdate(int $commandeId, ?int $userId): ?Commande
    {
        return Commande::query()
            ->whereKey($commandeId)
            ->when(
                $userId !== null,
                fn ($query) => $query->where('user_id', $userId)
            )
            ->lockForUpdate()
            ->first();
    }

    public function create(array $data): Commande
    {
        return Commande::create($data);
    }

    public function createDetail(array $data): CommandeDetail
    {
        return CommandeDetail::create($data);
    }

    public function updateStock(Produit $produit, int $quantite): void
    {
        $produit->decrement('stock', $quantite);
    }

    public function updateStatus(Commande $commande, string $statut): Commande
    {
        $commande->statut = $statut;
        $commande->save();

        return $commande->refresh();
    }

    public function paginate(?int $userId = null): LengthAwarePaginator
    {
        return Commande::query()
            ->with(['details.produit'])
            ->when(
                $userId !== null,
                fn ($query) => $query->where('user_id', $userId)
            )
            ->latest()
            ->paginate(10);
    }

    public function findById(int $commandeId, ?int $userId = null): ?Commande
    {
        return Commande::query()
            ->with(['details.produit'])
            ->whereKey($commandeId)
            ->when(
                $userId !== null,
                fn ($query) => $query->where('user_id', $userId)
            )
            ->first();
    }
}

<?php

namespace App\Services\Produit\Implementations;

use App\DTO\Produit\CreateProduitDTO;
use App\DTO\Produit\ProduitFiltersDTO;
use App\DTO\Produit\UpdateProduitDTO;
use App\Exceptions\ResourceInUseException;
use App\Models\Produit;
use App\Repositories\Produit\Interfaces\ProduitRepositoryInterface;
use App\Services\Produit\Interfaces\ProduitServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProduitService implements ProduitServiceInterface
{
    public function __construct(
        private readonly ProduitRepositoryInterface $produitRepository
    ) {}

    public function getAll(ProduitFiltersDTO $filters): LengthAwarePaginator
    {
        return $this->produitRepository->search($filters);
    }

    public function getById(int $id): Produit
    {
        $produit = $this->produitRepository->getById($id);

        if (! $produit) {
            throw new ModelNotFoundException(
                'Produit introuvable.'
            );
        }

        return $produit;
    }

    public function create(CreateProduitDTO $dto): Produit
    {
        return $this->produitRepository->create([
            'nom' => $dto->nom,
            'prix' => $dto->prix,
            'categorie_id' => $dto->categorie_id,
            'stock' => $dto->stock,
            'is_active' => $dto->is_active,
        ]);
    }

    public function update(int $id, UpdateProduitDTO $dto): Produit
    {
        $produit = $this->produitRepository->getByIdForManagement($id);

        if (! $produit) {
            throw new ModelNotFoundException('Produit introuvable.');
        }

        return $this->produitRepository->update(
            $produit,
            $dto->toArray()
        );
    }

    public function delete(int $id): void
    {
        $produit = $this->produitRepository->getByIdForManagement($id);

        if (! $produit) {
            throw new ModelNotFoundException('Produit introuvable.');
        }

        if ($this->produitRepository->hasOrderDetails($produit)) {
            throw new ResourceInUseException(
                'Ce produit ne peut pas être supprimé car il est utilisé dans une commande.'
            );
        }

        $this->produitRepository->delete($produit);
    }
}

<?php

namespace App\Services\Implementations;

use App\DTO\CategorieDTO;
use App\DTO\UpdateCategorieDTO;
use App\Exceptions\ResourceInUseException;
use App\Models\Categorie;
use App\Repositories\Interfaces\CategorieRepositoryInterface;
use App\Services\Interfaces\CategorieServiceInterface;
use Illuminate\Support\Collection;

class CategorieService implements CategorieServiceInterface
{
    public function __construct(
        private CategorieRepositoryInterface $categorieRepository
    ) {}

    public function all(): Collection
    {
        return $this->categorieRepository->all();
    }

    public function findById(int $id): ?Categorie
    {
        return $this->categorieRepository->findById($id);
    }

    public function create(CategorieDTO $dto): Categorie
    {
        return $this->categorieRepository->create(
            $dto->toArray()
        );
    }

    public function update(int $id, UpdateCategorieDTO $dto): ?Categorie
    {
        $categorie = $this->categorieRepository->findByIdForManagement($id);

        if (! $categorie) {
            return null;
        }

        return $this->categorieRepository->update(
            $categorie,
            $dto->toArray()
        );
    }

    public function delete(int $id): bool
    {
        $categorie = $this->categorieRepository->findByIdForManagement($id);

        if (! $categorie) {
            return false;
        }

        if ($this->categorieRepository->hasProducts($categorie)) {
            throw new ResourceInUseException(
                'Cette catégorie ne peut pas être supprimée car elle contient des produits.'
            );
        }

        return $this->categorieRepository->delete($categorie);
    }
}

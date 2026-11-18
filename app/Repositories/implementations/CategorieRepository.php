<?php

namespace App\Repositories\Implementations;

use App\Models\Categorie;
use App\Repositories\Interfaces\CategorieRepositoryInterface;
use Illuminate\Support\Collection;

class CategorieRepository implements CategorieRepositoryInterface
{
    public function all(): Collection
    {
        return Categorie::query()
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    public function findById(int $id): ?Categorie
    {
        return Categorie::query()
            ->where('is_active', true)
            ->find($id);
    }

    public function findByIdForManagement(int $id): ?Categorie
    {
        return Categorie::find($id);
    }

    public function hasProducts(Categorie $categorie): bool
    {
        return $categorie->produits()->exists();
    }

    public function create(array $data): Categorie
    {
        return Categorie::create($data);
    }

    public function update(Categorie $categorie, array $data): Categorie
    {
        $categorie->update($data);

        return $categorie;
    }

    public function delete(Categorie $categorie): bool
    {
        return $categorie->delete();
    }
}

<?php

namespace App\Repositories\Interfaces;

use App\Models\Categorie;
use Illuminate\Support\Collection;

interface CategorieRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Categorie;

    public function findByIdForManagement(int $id): ?Categorie;

    public function hasProducts(Categorie $categorie): bool;

    public function create(array $data): Categorie;

    public function update(Categorie $categorie, array $data): Categorie;

    public function delete(Categorie $categorie): bool;
}

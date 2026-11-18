<?php

namespace App\Services\Interfaces;

use App\DTO\CategorieDTO;
use App\DTO\UpdateCategorieDTO;
use App\Models\Categorie;
use Illuminate\Support\Collection;

interface CategorieServiceInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Categorie;

    public function create(CategorieDTO $dto): Categorie;

    public function update(int $id, UpdateCategorieDTO $dto): ?Categorie;

    public function delete(int $id): bool;
}
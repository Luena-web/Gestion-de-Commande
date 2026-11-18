<?php

namespace App\Services\Produit\Interfaces;

use App\DTO\Produit\CreateProduitDTO;
use App\DTO\Produit\ProduitFiltersDTO;
use App\DTO\Produit\UpdateProduitDTO;
use App\Models\Produit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProduitServiceInterface
{
    public function getAll(ProduitFiltersDTO $filters): LengthAwarePaginator;

    public function getById(int $id): Produit;

    public function create(CreateProduitDTO $dto): Produit;

    public function update(int $id, UpdateProduitDTO $dto): Produit;

    public function delete(int $id): void;
}

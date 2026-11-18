<?php

namespace App\Repositories\Produit\Interfaces;

use App\DTO\Produit\ProduitFiltersDTO;
use App\Models\Produit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProduitRepositoryInterface
{
    public function search(ProduitFiltersDTO $filters): LengthAwarePaginator;

    public function getById(int $id): ?Produit;

    public function getByIdForManagement(int $id): ?Produit;

    public function create(array $data): Produit;

    public function update(Produit $produit, array $data): Produit;

    public function delete(Produit $produit): bool;

    public function hasOrderDetails(Produit $produit): bool;
}

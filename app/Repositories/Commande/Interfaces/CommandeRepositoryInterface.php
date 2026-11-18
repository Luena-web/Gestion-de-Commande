<?php

namespace App\Repositories\Commande\Interfaces;

use App\Models\Commande;
use App\Models\CommandeDetail;
use App\Models\Produit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CommandeRepositoryInterface
{
    public function findProductsByIdsForUpdate(array $productIds): Collection;

    public function findByIdForUpdate(int $commandeId, ?int $userId): ?Commande;

    public function create(array $data): Commande;

    public function createDetail(array $data): CommandeDetail;

    public function updateStock(Produit $produit, int $quantite): void;

    public function updateStatus(Commande $commande, string $statut): Commande;

    public function paginate(?int $userId = null): LengthAwarePaginator;

    public function findById(int $commandeId, ?int $userId = null): ?Commande;
}

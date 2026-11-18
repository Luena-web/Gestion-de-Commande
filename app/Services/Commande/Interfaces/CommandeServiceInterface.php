<?php

namespace App\Services\Commande\Interfaces;

use App\DTO\Commande\CommandeDTO;
use App\Models\Commande;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CommandeServiceInterface
{
    public function create(CommandeDTO $dto): Commande;

    public function confirm(int $commandeId, ?int $userId): Commande;

    public function paginate(?int $userId = null): LengthAwarePaginator;

    public function show(int $commandeId, ?int $userId = null): Commande;
}

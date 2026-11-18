<?php

namespace App\DTO\Produit;

class ProduitFiltersDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $categorie_id = null,
        public readonly ?string $prix_min = null,
        public readonly ?string $prix_max = null,
        public readonly int $per_page = 15,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: isset($data['search']) ? trim($data['search']) : null,
            categorie_id: isset($data['categorie_id'])
                ? (int) $data['categorie_id']
                : null,
            prix_min: isset($data['prix_min']) ? (string) $data['prix_min'] : null,
            prix_max: isset($data['prix_max']) ? (string) $data['prix_max'] : null,
            per_page: isset($data['per_page']) ? (int) $data['per_page'] : 15,
        );
    }
}

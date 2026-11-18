<?php

namespace App\DTO\Produit;

class CreateProduitDTO
{
    public function __construct(
        public readonly string $nom,
        public readonly string $prix,
        public readonly int $categorie_id,
        public readonly int $stock,
        public readonly bool $is_active,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nom: $data['nom'],
            prix: (string) $data['prix'],
            categorie_id: (int) $data['categorie_id'],
            stock: (int) ($data['stock'] ?? 0),
            is_active: (bool) ($data['is_active'] ?? true),
        );
    }
}
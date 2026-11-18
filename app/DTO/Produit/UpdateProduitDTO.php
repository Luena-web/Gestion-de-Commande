<?php

namespace App\DTO\Produit;

class UpdateProduitDTO
{
    public function __construct(
        public readonly ?string $nom = null,
        public readonly ?string $prix = null,
        public readonly ?int $categorie_id = null,
        public readonly ?int $stock = null,
        public readonly ?bool $is_active = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nom: $data['nom'] ?? null,
            prix: isset($data['prix']) ? (string) $data['prix'] : null,
            categorie_id: isset($data['categorie_id'])
                ? (int) $data['categorie_id']
                : null,
            stock: isset($data['stock'])
                ? (int) $data['stock']
                : null,
            is_active: isset($data['is_active'])
                ? (bool) $data['is_active']
                : null,
        );
    }

    public function toArray(): array
    {
        return array_filter(
            [
                'nom' => $this->nom,
                'prix' => $this->prix,
                'categorie_id' => $this->categorie_id,
                'stock' => $this->stock,
                'is_active' => $this->is_active,
            ],
            fn ($value) => $value !== null
        );
    }
}
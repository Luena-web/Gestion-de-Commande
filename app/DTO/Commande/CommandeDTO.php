<?php

namespace App\DTO\Commande;

class CommandeDTO
{
    public function __construct(
        public readonly int $user_id,
        public readonly array $details,
    ) {}

    /**
     * Créer un DTO à partir d'un tableau.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            user_id: $data['user_id'],
            details: $data['details'],
        );
    }

    /**
     * Transformer le DTO en tableau.
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'details' => $this->details,
        ];
    }
}

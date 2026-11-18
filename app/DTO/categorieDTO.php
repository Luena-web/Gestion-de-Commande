<?php
namespace App\DTO;

class CategorieDTO{

public function __construct(
    public string $nom,
    public ?string $description,
    public bool $is_active,


){}

 public static function fromArray(array $data): self
    {
        return new self(
            nom: $data['nom'],
            description: $data['description'] ?? null,
            is_active: $data['is_active'] ?? true
        );
    }
public function toArray(): array
    {
        return [
            'nom' => $this->nom,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];
    }

}
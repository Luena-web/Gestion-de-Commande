<?php
namespace App\DTO;

class UpdateCategorieDTO
{
    public function __construct(
        private array $attributes
    ) {}

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}

<?php

namespace App\Repositories\Produit\Implementations;

use App\DTO\Produit\ProduitFiltersDTO;
use App\Models\CommandeDetail;
use App\Models\Produit;
use App\Repositories\Produit\Interfaces\ProduitRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProduitRepository implements ProduitRepositoryInterface
{
    public function search(ProduitFiltersDTO $filters): LengthAwarePaginator
    {
        return Produit::query()
            ->with('categorie')
            ->where('is_active', true)
            ->whereHas('categorie', fn ($query) => $query->where('is_active', true))
            ->when(
                $filters->search !== null && $filters->search !== '',
                fn ($query) => $query->where('nom', 'like', '%'.$filters->search.'%')
            )
            ->when(
                $filters->categorie_id !== null,
                fn ($query) => $query->where('categorie_id', $filters->categorie_id)
            )
            ->when(
                $filters->prix_min !== null,
                fn ($query) => $query->where('prix', '>=', $filters->prix_min)
            )
            ->when(
                $filters->prix_max !== null,
                fn ($query) => $query->where('prix', '<=', $filters->prix_max)
            )
            ->latest()
            ->paginate($filters->per_page)
            ->withQueryString();
    }

    public function getById(int $id): ?Produit
    {
        return Produit::query()
            ->with('categorie')
            ->where('is_active', true)
            ->whereHas('categorie', fn ($query) => $query->where('is_active', true))
            ->find($id);
    }

    public function getByIdForManagement(int $id): ?Produit
    {
        return Produit::with('categorie')->find($id);
    }

    public function create(array $data): Produit
    {
        return Produit::create($data);
    }

    public function update(Produit $produit, array $data): Produit
    {
        $produit->update($data);

        return $produit->refresh();
    }

    public function delete(Produit $produit): bool
    {
        return (bool) $produit->delete();
    }

    public function hasOrderDetails(Produit $produit): bool
    {
        return CommandeDetail::query()
            ->where('produit_id', $produit->id)
            ->exists();
    }
}

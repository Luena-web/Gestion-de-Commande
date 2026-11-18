<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\Produit\CreateProduitDTO;
use App\DTO\Produit\ProduitFiltersDTO;
use App\DTO\Produit\UpdateProduitDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListProduitRequest;
use App\Http\Requests\StoreProduitRequest;
use App\Http\Requests\UpdateProduitRequest;
use App\Http\Resources\ProduitResource;
use App\Services\Produit\Interfaces\ProduitServiceInterface;
use Illuminate\Http\JsonResponse;

class ProduitController extends Controller
{
    public function __construct(
        private readonly ProduitServiceInterface $produitService
    ) {}

    /**
     * Afficher tous les produits.
     */
    public function index(ListProduitRequest $request): JsonResponse
    {
        $filters = ProduitFiltersDTO::fromArray($request->validated());
        $produits = $this->produitService->getAll($filters);

        return ProduitResource::collection($produits)
            ->additional([
                'message' => 'Liste des produits récupérée avec succès.',
            ])
            ->response();
    }

    /**
     * Afficher un produit.
     */
    public function show(int $id): JsonResponse
    {
        $produit = $this->produitService->getById($id);

        return response()->json([
            'message' => 'Détails du produit.',
            'data' => new ProduitResource($produit),
        ]);
    }

    /**
     * Enregistrer un produit.
     */
    public function store(StoreProduitRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dto = CreateProduitDTO::fromArray($data);

        $produit = $this->produitService->create($dto);

        return response()->json([
            'message' => 'Produit créé avec succès.',
            'data' => new ProduitResource($produit),
        ], 201);
    }

    /**
     * Modifier un produit.
     */
    public function update(
        UpdateProduitRequest $request,
        int $id
    ): JsonResponse {
        $data = $request->validated();

        $dto = UpdateProduitDTO::fromArray($data);

        $produit = $this->produitService->update($id, $dto);

        return response()->json([
            'message' => 'Produit modifié avec succès.',
            'data' => new ProduitResource($produit),
        ]);
    }

    /**
     * Supprimer un produit.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->produitService->delete($id);

        return response()->json([
            'message' => 'Produit supprimé avec succès.',
        ]);
    }
}

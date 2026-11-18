<?php

namespace App\Http\Controllers\Api\V1;
use App\Services\Interfaces\CategorieServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategorieRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateCategorieRequest;
use App\Http\Resources\CategorieResource;
use App\DTO\CategorieDTO;
use App\DTO\UpdateCategorieDTO;




class CategorieController extends Controller
{
    public function __construct(
    private CategorieServiceInterface $categorieService
) {}
    public function index():JsonResponse
    {
        $categories = $this->categorieService->all();
        return response()->json([
            'status'=>'success',
            'message'=>'Liste des categories',
            // 'data'=>$categories
            'data'=> CategorieResource::collection($categories)
        ],200);
    }

    public function show(int $id): JsonResponse
    {
        // $categorie = Categorie::with('produits')->findOrFail($id);
        $categorie = $this->categorieService->findById($id);
 if (!$categorie) {
        return response()->json([
            'status' => 'error',
            'message' => 'Catégorie introuvable'
        ], 404);
    }
        return response()->json([
            'status' => 'success',
            'message' => 'Categorie details',
         //   'data' =>  $categorie
            'data' => new CategorieResource( $categorie)
        ], 200);
    }

    public function store(StoreCategorieRequest $request): JsonResponse
    { 
        // Recup données valider par FormRequest
        $data = $request->validated();
        //Transforme le tableau en un objet en un objet
        $dto = CategorieDTO:: fromArray($data);
        // Transformation du DTO en tableau
        //Créer la catégorie à partir des données du DTO
      $categorie = $this->categorieService->create($dto);

        return response()->json([
            'status' => 'success',
            'message' => 'Categorie created successfully',
            'data' =>new CategorieResource( $categorie)
        ], 201);
    }

public function update(UpdateCategorieRequest $request, int $id): JsonResponse
{
    // 1. Récupérer uniquement les données validées
    $validatedData = $request->validated();

    // 2. Transformer les données en DTO
    $dto = UpdateCategorieDTO::fromArray($validatedData);

    // 3. Demander au Service de modifier la catégorie
    $categorie = $this->categorieService->update($id, $dto);

    // 4. Vérifier si la catégorie existe
    if (!$categorie) {
        return response()->json([
            'status' => 'error',
            'message' => 'Catégorie introuvable'
        ], 404);
    }

    // 5. Retourner la réponse API
    return response()->json([
        'status' => 'success',
        'message' => 'Catégorie modifiée avec succès',
        'data' => new CategorieResource($categorie)
    ], 200);
}

  
public function destroy(int $id): JsonResponse
{
    // 1. Demander au Service de supprimer la catégorie
    $deleted = $this->categorieService->delete($id);

    // 2. Vérifier si la suppression a réussi
    if (!$deleted) {
        return response()->json([
            'status' => 'error',
            'message' => 'Catégorie introuvable'
        ], 404);
    }

    // 3. Retourner une confirmation
    return response()->json([
        'status' => 'success',
        'message' => 'Catégorie supprimée avec succès'
    ], 200);
}

}


<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategorieController;
use App\Http\Controllers\Api\V1\CommandeController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\ProduitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ========================================
// ROUTES PUBLIQUES
// ========================================

// Récupérer les informations de l'utilisateur connecté
// Cette route nécessite une authentification.
Route::get('v1/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Inscription
Route::post('v1/register', [AuthController::class, 'register']);

// Connexion Mobile
Route::post('v1/mobile/login', [LoginController::class, 'loginMobile']);

// ========================================
// CATALOGUE PUBLIC
// ========================================

Route::prefix('v1')->group(function () {

    // Consultation des catégories
    Route::get('/categories', [CategorieController::class, 'index']);
    Route::get('/categories/{id}', [CategorieController::class, 'show']);

    // Consultation des produits
    Route::get('/produits', [ProduitController::class, 'index']);
    Route::get('/produits/{id}', [ProduitController::class, 'show']);

});

// ========================================
// ROUTES PROTÉGÉES
// ========================================

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/mobile/logout', [LoginController::class, 'logoutMobile']);

    Route::get('/commandes', [CommandeController::class, 'index']);
    Route::get('/commandes/{id}', [CommandeController::class, 'show']);
    Route::post('/commandes', [CommandeController::class, 'store']);
    Route::patch('/commandes/{id}/confirm', [CommandeController::class, 'confirm']);

    Route::middleware('admin')->group(function () {
        // Gestion du catalogue réservée aux administrateurs.
        Route::post('/categories', [CategorieController::class, 'store']);
        Route::put('/categories/{id}', [CategorieController::class, 'update']);
        Route::patch('/categories/{id}', [CategorieController::class, 'update']);
        Route::delete('/categories/{id}', [CategorieController::class, 'destroy']);

        Route::post('/produits', [ProduitController::class, 'store']);
        Route::put('/produits/{id}', [ProduitController::class, 'update']);
        Route::patch('/produits/{id}', [ProduitController::class, 'update']);
        Route::delete('/produits/{id}', [ProduitController::class, 'destroy']);
    });
});

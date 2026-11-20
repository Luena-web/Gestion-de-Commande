<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategorieController;
use App\Http\Controllers\Api\V1\CommandeController;
use App\Http\Controllers\Api\V1\GoogleAuthController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\ProduitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ========================================
// ROUTES PUBLIQUES
// ========================================

// Inscription
Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/google/login', [GoogleAuthController::class, 'login'])
    ->middleware('throttle:10,1');

// Authentification React Web (token Sanctum)
Route::post('v1/web/login', [LoginController::class, 'loginWeb']);

// Authentification React Native (session)
Route::post('v1/mobile/login', [LoginController::class, 'loginMobile']);

// ========================================
// CATALOGUE PUBLIC
// ========================================

Route::prefix('v1')->group(function () {
    Route::get('/categories', [CategorieController::class, 'index']);
    Route::get('/categories/{id}', [CategorieController::class, 'show']);

    Route::get('/produits', [ProduitController::class, 'index']);
    Route::get('/produits/{id}', [ProduitController::class, 'show']);
});

// ========================================
// ROUTES PROTÉGÉES
// ========================================

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Déconnexion React Web
    Route::post('/web/logout', [LoginController::class, 'logoutWeb']);

    // Déconnexion React Native
    Route::post('/mobile/logout', [LoginController::class, 'logoutMobile']);

    // Informations utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Gestion des commandes
    Route::get('/commandes', [CommandeController::class, 'index']);
    Route::get('/commandes/{id}', [CommandeController::class, 'show']);
    Route::post('/commandes', [CommandeController::class, 'store']);
    Route::patch('/commandes/{id}/confirm', [CommandeController::class, 'confirm']);

    // Gestion réservée aux administrateurs
    Route::middleware('admin')->group(function () {

        // Catégories
        Route::post('/categories', [CategorieController::class, 'store']);
        Route::put('/categories/{id}', [CategorieController::class, 'update']);
        Route::patch('/categories/{id}', [CategorieController::class, 'update']);
        Route::delete('/categories/{id}', [CategorieController::class, 'destroy']);

        // Produits
        Route::post('/produits', [ProduitController::class, 'store']);
        Route::put('/produits/{id}', [ProduitController::class, 'update']);
        Route::patch('/produits/{id}', [ProduitController::class, 'update']);
        Route::delete('/produits/{id}', [ProduitController::class, 'destroy']);
    });
});

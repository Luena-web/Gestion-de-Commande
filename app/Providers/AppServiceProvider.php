<?php

namespace App\Providers;

use App\Repositories\Auth\Implementations\AuthRepository;
use App\Repositories\Auth\Interfaces\AuthRepositoryInterface;
use App\Repositories\Commande\Implementations\CommandeRepository;
use App\Repositories\Commande\Interfaces\CommandeRepositoryInterface;
use App\Repositories\Implementations\CategorieRepository;
use App\Repositories\Interfaces\CategorieRepositoryInterface;
use App\Repositories\Login\Implementations\LoginRepository;
use App\Repositories\Login\Interfaces\LoginRepositoryInterface;
use App\Repositories\Produit\Implementations\ProduitRepository;
use App\Repositories\Produit\Interfaces\ProduitRepositoryInterface;
use App\Services\Auth\Implementations\AuthService;
use App\Services\Auth\Implementations\GoogleAuthService;
use App\Services\Auth\Implementations\GoogleIdentityVerifier;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Services\Auth\Interfaces\GoogleAuthServiceInterface;
use App\Services\Auth\Interfaces\GoogleIdentityVerifierInterface;
use App\Services\Commande\Implementations\CommandeService;
use App\Services\Commande\Interfaces\CommandeServiceInterface;
use App\Services\Implementations\CategorieService;
use App\Services\Interfaces\CategorieServiceInterface;
use App\Services\Login\Implementations\LoginService;
use App\Services\Login\Interfaces\LoginServiceInterface;
use App\Services\Produit\Implementations\ProduitService;
use App\Services\Produit\Interfaces\ProduitServiceInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CategorieRepositoryInterface::class,
            CategorieRepository::class
        );
        $this->app->bind(
            CategorieServiceInterface::class,
            CategorieService::class
        );
        $this->app->bind(
            ProduitRepositoryInterface::class,
            ProduitRepository::class
        );

        $this->app->bind(
            ProduitServiceInterface::class,
            ProduitService::class
        );

        $this->app->bind(
            AuthRepositoryInterface::class,
            AuthRepository::class
        );

        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );
        $this->app->bind(
            GoogleIdentityVerifierInterface::class,
            GoogleIdentityVerifier::class
        );
        $this->app->bind(
            GoogleAuthServiceInterface::class,
            GoogleAuthService::class
        );

        $this->app->bind(
            LoginRepositoryInterface::class,
            LoginRepository::class
        );
        $this->app->bind(
            LoginServiceInterface::class,
            LoginService::class
        );

        $this->app->bind(
            CommandeRepositoryInterface::class,
            CommandeRepository::class
        );

        $this->app->bind(
            CommandeServiceInterface::class,
            CommandeService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}

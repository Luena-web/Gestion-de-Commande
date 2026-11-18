<?php

namespace App\Providers;
use App\Repositories\Interfaces\CategorieRepositoryInterface;
use App\Repositories\Implementations\CategorieRepository;
use App\Services\Interfaces\CategorieServiceInterface;
use App\Services\Implementations\CategorieService;
use App\Repositories\Produit\Interfaces\ProduitRepositoryInterface;
use App\Repositories\Produit\Implementations\ProduitRepository;
use App\Repositories\Commande\Interfaces\CommandeRepositoryInterface;
use App\Repositories\Commande\Implementations\CommandeRepository;
use App\Repositories\Auth\Interfaces\AuthRepositoryInterface;
use App\Repositories\Auth\Implementations\AuthRepository;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use App\Services\Auth\Implementations\AuthService;
use App\Services\Commande\Interfaces\CommandeServiceInterface;
use App\Services\Commande\Implementations\CommandeService;
use App\Repositories\Login\Interfaces\LoginRepositoryInterface;
use App\Repositories\Login\Implementations\LoginRepository;
use App\Services\Login\Interfaces\LoginServiceInterface;
use App\Services\Login\Implementations\LoginService;
use App\Services\Produit\Interfaces\ProduitServiceInterface;
use App\Services\Produit\Implementations\ProduitService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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

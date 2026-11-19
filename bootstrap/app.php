
<?php

use App\Exceptions\ResourceInUseException;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Activation de Sanctum pour les requêtes SPA stateful
        $middleware->statefulApi();
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
        $middleware->trustProxies(at: '*');

        // Ne pas rediriger les utilisateurs non authentifiés
        // vers une page de connexion Web.
        $middleware->redirectGuestsTo('/up');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ResourceInUseException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        });
        

        // Retourner des réponses JSON pour les requêtes API.
        $exceptions->shouldRenderJsonWhen(
            function ($request, Throwable $e) {
                return $request->is('api/*')
                    || $request->expectsJson();
            }
        );
    })
    ->create();

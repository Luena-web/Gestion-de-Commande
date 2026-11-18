<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Login\Interfaces\LoginServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class LoginController extends Controller
{
    public function __construct(
        private readonly LoginServiceInterface $loginService
    ) {}

    /**
     * Connexion pour React Web
     */
    public function loginWeb(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::fromArray($request->validated());

        $user = $this->loginService->login($dto);

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Connexion réussie.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Fermer la session web courante.
     */
    public function logoutWeb(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }

    /**
     * Connexion pour React Native
     */
    public function loginMobile(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::fromArray($request->validated());

        $user = $this->loginService->login($dto);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'token' => $token,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Révoquer le jeton mobile actuellement utilisé.
     */
    public function logoutMobile(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }
}

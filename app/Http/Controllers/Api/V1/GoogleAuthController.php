<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\Interfaces\GoogleAuthServiceInterface;
use Illuminate\Http\JsonResponse;

class GoogleAuthController extends Controller
{
    public function __construct(
        private readonly GoogleAuthServiceInterface $googleAuthService
    ) {}

    public function login(GoogleLoginRequest $request): JsonResponse
    {
        $user = $this->googleAuthService->authenticate(
            $request->validated('credential')
        );

        return response()->json([
            'message' => 'Connexion Google réussie.',
            'token' => $user->createToken('google')->plainTextToken,
            'data' => new UserResource($user),
        ]);
    }
}

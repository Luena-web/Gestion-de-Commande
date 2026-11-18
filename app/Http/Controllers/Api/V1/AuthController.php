<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\Auth\RegisterUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\Interfaces\AuthServiceInterface;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dto = RegisterUserDTO::fromArray($data);

        $user = $this->authService->register($dto);

        return response()->json([
            'message' => 'Compte créé avec succès.',
            'data' => new UserResource($user),
        ], 201);
    }
}

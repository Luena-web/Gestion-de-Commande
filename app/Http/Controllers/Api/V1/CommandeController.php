<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\Commande\CommandeDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommandeRequest;
use App\Http\Resources\Commande\CommandeResource;
use App\Services\Commande\Interfaces\CommandeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function __construct(
        private readonly CommandeServiceInterface $commandeService
    ) {}

    /**
     * Lister les commandes.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $userId = $user->role === 'admin'
            ? null
            : $user->id;

        $commandes = $this->commandeService->paginate($userId);

        return CommandeResource::collection($commandes)
            ->response();
    }

    /**
     * Créer une nouvelle commande.
     */
    public function store(CommandeRequest $request): JsonResponse
    {
        $dto = CommandeDTO::fromArray([
            'user_id' => $request->user()->id,
            'details' => $request->validated('details'),
        ]);

        $commande = $this->commandeService->create($dto);

        return (new CommandeResource($commande))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Consulter une commande précise.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $userId = $user->role === 'admin'
            ? null
            : (int) $user->id;

        $commande = $this->commandeService->show(
            $id,
            $userId
        );

        return (new CommandeResource($commande))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Confirmer une commande.
     */
    public function confirm(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $userId = $user->role === 'admin'
            ? null
            : (int) $user->id;

        $commande = $this->commandeService->confirm(
            $id,
            $userId
        );

        return (new CommandeResource($commande))
            ->response()
            ->setStatusCode(200);
    }
}

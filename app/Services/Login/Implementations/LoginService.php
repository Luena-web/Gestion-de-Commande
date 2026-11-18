<?php
namespace App\Services\Login\Implementations;
use App\DTO\Auth\LoginDTO;
use App\Models\User;
use App\Repositories\Login\Interfaces\LoginRepositoryInterface;
use App\Services\Login\Interfaces\LoginServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginService implements LoginServiceInterface
{
    public function __construct(
        private readonly LoginRepositoryInterface $loginRepository
    ) {}

    public function login(LoginDTO $dto): User
    {
        $user = $this->loginRepository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        return $user;
    }
}

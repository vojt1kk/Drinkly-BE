<?php

namespace App\Actions\Auth;

use App\Data\Auth\LoginByEmailAndPasswordData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final readonly class LoginByEmailAndPasswordAction
{
    public function execute(LoginByEmailAndPasswordData $inputData): array
    {
        $user = User::query()
            ->whereEmail($inputData->email)
            ->first();

        if (! $user || ! Hash::check($inputData->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}



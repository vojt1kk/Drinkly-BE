<?php

namespace App\Actions\Auth;

use App\Data\Users\UserInputData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class RegisterAction
{
    public function execute(UserInputData $inputData): array
    {
        $user = User::create([
            'name' => $inputData->getName(),
            'email' => $inputData->getEmail(),
            'password' => Hash::make($inputData->getPassword()),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}



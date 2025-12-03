<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Actions\Auth\RegisterAction;
use App\Actions\Auth\LoginByEmailAndPasswordAction;
use App\Http\Requests\Api\LoginByEmailAndPasswordRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly RegisterAction $registerAction,
        private readonly LoginByEmailAndPasswordAction $loginByEmailAndPasswordAction,
    ) {
    }

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerAction->execute($request->toInputData());

        return response()->json($result, 201);
    }

    /**
     * Login user
     */
    public function login(LoginByEmailAndPasswordRequest $request): JsonResponse
    {
        $result = $this->loginByEmailAndPasswordAction->execute($request->toData());

        return response()->json($result);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user !== null) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}


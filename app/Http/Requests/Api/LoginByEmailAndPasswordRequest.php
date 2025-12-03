<?php

namespace App\Http\Requests\Api;

use App\Data\Auth\LoginByEmailAndPasswordData;
use Illuminate\Foundation\Http\FormRequest;

class LoginByEmailAndPasswordRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toData(): LoginByEmailAndPasswordData
    {
        $validated = $this->validated();

        return new LoginByEmailAndPasswordData(
            email: $validated['email'],
            password: $validated['password'],
        );
    }
}



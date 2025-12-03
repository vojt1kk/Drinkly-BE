<?php

namespace App\Http\Requests\Api;

use App\Data\Users\UserInputData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => [
                'required',
                'string',
                Password::default(),
            ],
            'password_confirmation' => [
                'required',
                'string',
                'same:password',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toInputData(): UserInputData
    {
        $validated = $this->validated();

        $inputData = new UserInputData();
        $inputData->setName($validated['name']);
        $inputData->setEmail($validated['email']);
        $inputData->setPassword($validated['password']);

        return $inputData;
    }
}



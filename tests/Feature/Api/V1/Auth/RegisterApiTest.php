<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\postJson;

it('registers a new user', function (): void {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ];

    postJson('/api/v1/register', $data)
        ->assertStatus(201)
        ->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            'token',
        ]);

    $user = User::query()
        ->whereEmail($data['email'])
        ->firstOrFail();

    expect($user)
        ->name->toBe($data['name'])
        ->email->toBe($data['email']);

    expect(Hash::isHashed($user->password))
        ->toBeTrue()
        ->and(Hash::check($data['password'], $user->password))
        ->toBeTrue();
});

it('fails 422 on invalid registration data', function (array $case): void {
    $payload = $case['payload'];
    $errors = $case['errors'];

    postJson('/api/v1/register', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors($errors);
})->with([
    'email already taken' => fn () => [
        'payload' => [
            'name' => 'John Doe',
            'email' => User::factory()->create()->email,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ],
        'errors' => ['email'],
    ],
    'password confirmation does not match' => fn () => [
        'payload' => [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ],
        'errors' => ['password_confirmation'],
    ],
]);



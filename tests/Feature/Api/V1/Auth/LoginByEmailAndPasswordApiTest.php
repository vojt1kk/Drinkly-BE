<?php

use App\Models\User;

use function Pest\Laravel\postJson;

it('logs in user with valid credentials', function (): void {
    /** @var User $user */
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $payload = [
        'email' => $user->email,
        'password' => 'password',
    ];

    postJson('/api/v1/login', $payload)
        ->assertStatus(200)
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
});

it('fails 422 with invalid credentials', function (array $payload): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    postJson('/api/v1/login', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'email' => [
                'The provided credentials are incorrect.',
            ],
        ]);
})->with([
    'wrong email' => fn () => [
        'email' => 'wrong@example.com',
        'password' => 'password',
    ],
    'wrong password' => fn () => [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ],
]);



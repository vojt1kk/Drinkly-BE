<?php

use App\Models\User;

use function Pest\Laravel\getJson;

it('returns current authenticated user', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    getJson('/api/v1/user')
        ->assertStatus(200)
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
});

it('returns 401 when not authenticated', function (): void {
    getJson('/api/v1/user')
        ->assertStatus(401);
});



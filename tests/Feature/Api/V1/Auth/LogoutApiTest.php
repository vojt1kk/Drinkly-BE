<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

it('logs out authenticated user', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    postJson('/api/v1/logout')
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Logged out successfully',
        ]);
});

it('returns 401 when logout without authentication', function (): void {
    postJson('/api/v1/logout')
        ->assertStatus(401);
});



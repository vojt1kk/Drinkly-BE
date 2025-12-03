<?php

use App\Models\User;
use App\Models\WaterIntake;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('stores water intake for authenticated user', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $payload = [
        'amount' => 250,
        'intake_time' => now()->toIso8601String(),
    ];

    postJson('/api/v1/water-intake', $payload)
        ->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'amount',
                'intake_time',
            ],
        ]);

    $this->assertDatabaseHas('water_intakes', [
        'user_id' => $user->id,
        'amount' => 250,
    ]);
});

it('requires authentication to store water intake', function (): void {
    postJson('/api/v1/water-intake', [
        'amount' => 250,
    ])->assertStatus(401);
});

it('lists water intakes for authenticated user', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $otherUser = User::factory()->create();

    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 200,
        'intake_time' => now()->subHour(),
    ]);

    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 300,
        'intake_time' => now(),
    ]);

    // intake for different user, should not appear
    WaterIntake::factory()->create([
        'user_id' => $otherUser->id,
        'amount' => 500,
        'intake_time' => now(),
    ]);

    getJson('/api/v1/water-intake')
        ->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['amount' => 200])
        ->assertJsonFragment(['amount' => 300])
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'amount', 'intake_time'],
            ],
            'meta' => [
                'date',
                'total_amount',
            ],
        ])
        ->assertJson([
            'meta' => [
                'total_amount' => 500, // 200 + 300
            ],
        ]);
});

it('filters water intakes by date when provided', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 200,
        'intake_time' => now()->subDay(),
    ]);

    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 300,
        'intake_time' => now(),
    ]);

    getJson('/api/v1/water-intake?date='.now()->toDateString())
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonMissing(['amount' => 200])
        ->assertJsonFragment(['amount' => 300])
        ->assertJson([
            'meta' => [
                'date' => now()->toDateString(),
                'total_amount' => 300,
            ],
        ]);
});

it('resets daily total to 0 for new day', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    // Create intake for yesterday
    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 500,
        'intake_time' => now()->subDay(),
    ]);

    // Today should have 0 total (no intakes today)
    getJson('/api/v1/water-intake')
        ->assertStatus(200)
        ->assertJson([
            'meta' => [
                'date' => now()->toDateString(),
                'total_amount' => 0,
            ],
        ]);

    // Yesterday should have 500
    getJson('/api/v1/water-intake?date='.now()->subDay()->toDateString())
        ->assertStatus(200)
        ->assertJson([
            'meta' => [
                'date' => now()->subDay()->toDateString(),
                'total_amount' => 500,
            ],
        ]);
});

it('returns weekly statistics', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $startOfWeek = now()->startOfWeek();

    // Create intakes for different days of the week
    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 200,
        'intake_time' => $startOfWeek->copy(),
    ]);

    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 300,
        'intake_time' => $startOfWeek->copy()->addDay(),
    ]);

    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 250,
        'intake_time' => $startOfWeek->copy()->addDays(2),
    ]);

    // Intake from last week should not be included
    WaterIntake::factory()->create([
        'user_id' => $user->id,
        'amount' => 1000,
        'intake_time' => $startOfWeek->copy()->subDay(),
    ]);

    getJson('/api/v1/water-intake/weekly-stats')
        ->assertStatus(200)
        ->assertJsonStructure([
            'week_start',
            'week_end',
            'total_amount',
            'daily_breakdown',
        ])
        ->assertJson([
            'total_amount' => 750, // 200 + 300 + 250
        ])
        ->assertJsonCount(7, 'daily_breakdown'); // 7 days in a week
});



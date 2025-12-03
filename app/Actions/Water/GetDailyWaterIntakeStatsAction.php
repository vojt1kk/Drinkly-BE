<?php

namespace App\Actions\Water;

use App\Models\User;
use Carbon\CarbonImmutable;

final readonly class GetDailyWaterIntakeStatsAction
{
    public function execute(User $user, ?string $date = null): array
    {
        $targetDate = $date !== null 
            ? CarbonImmutable::parse($date) 
            : CarbonImmutable::today();

        $totalAmount = $user->waterIntakes()
            ->whereDate('intake_time', $targetDate->toDateString())
            ->sum('amount');

        return [
            'date' => $targetDate->toDateString(),
            'total_amount' => (int) $totalAmount,
        ];
    }
}


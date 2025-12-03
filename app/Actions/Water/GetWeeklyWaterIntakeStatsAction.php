<?php

namespace App\Actions\Water;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

final readonly class GetWeeklyWaterIntakeStatsAction
{
    public function execute(User $user, ?string $startDate = null): array
    {
        $start = $startDate !== null 
            ? CarbonImmutable::parse($startDate)->startOfWeek()
            : CarbonImmutable::today()->startOfWeek();

        $end = $start->copy()->endOfWeek();

        $totalAmount = $user->waterIntakes()
            ->whereBetween('intake_time', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        // Get daily breakdown for the week
        $dailyStats = $user->waterIntakes()
            ->whereBetween('intake_time', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('DATE(intake_time) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => (int) $item->total];
            });

        // Fill in missing days with 0
        $weekDays = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $dateKey = $current->toDateString();
            $weekDays[$dateKey] = $dailyStats->get($dateKey, 0);
            $current = $current->addDay();
        }

        return [
            'week_start' => $start->toDateString(),
            'week_end' => $end->toDateString(),
            'total_amount' => (int) $totalAmount,
            'daily_breakdown' => $weekDays,
        ];
    }
}


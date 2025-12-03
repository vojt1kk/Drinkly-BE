<?php

namespace App\Actions\Water;

use App\Models\User;
use App\Models\WaterIntake;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

final readonly class ListWaterIntakesAction
{
    /**
     * @return Collection<int, WaterIntake>
     */
    public function execute(User $user, ?string $date = null): Collection
    {
        $query = WaterIntake::query()
            ->where('user_id', $user->id)
            ->orderByDesc('intake_time');

        if ($date !== null) {
            $carbonDate = CarbonImmutable::parse($date);

            $query->whereDate('intake_time', $carbonDate->toDateString());
        }

        return $query->get();
    }
}



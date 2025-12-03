<?php

namespace App\Actions\Water;

use App\Data\Water\WaterIntakeData;
use App\Models\User;
use App\Models\WaterIntake;
use DateTimeInterface;

final readonly class StoreWaterIntakeAction
{
    public function execute(User $user, WaterIntakeData $data): WaterIntake
    {
        $intakeTime = $data->intakeTime instanceof DateTimeInterface
            ? $data->intakeTime
            : now();

        return WaterIntake::query()->create([
            'user_id' => $user->id,
            'amount' => $data->amount,
            'intake_time' => $intakeTime,
        ]);
    }
}



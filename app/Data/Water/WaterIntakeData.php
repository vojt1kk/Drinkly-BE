<?php

namespace App\Data\Water;

final readonly class WaterIntakeData
{
    public function __construct(
        public int $amount,
        public \DateTimeInterface $intakeTime,
    ) {
    }
}



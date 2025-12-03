<?php

namespace App\Http\Requests\Api\WaterIntake;

use App\Data\Water\WaterIntakeData;
use Illuminate\Foundation\Http\FormRequest;

class StoreWaterIntakeRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'integer',
                'min:1',
            ],
            'intake_time' => [
                'nullable',
                'date',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function toData(): WaterIntakeData
    {
        $validated = $this->validated();

        return new WaterIntakeData(
            amount: $validated['amount'],
            intakeTime: isset($validated['intake_time']) ? new \DateTimeImmutable($validated['intake_time']) : null,
        );
    }
}



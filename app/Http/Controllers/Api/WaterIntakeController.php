<?php

namespace App\Http\Controllers\Api;

use App\Actions\Water\ListWaterIntakesAction;
use App\Actions\Water\StoreWaterIntakeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WaterIntake\StoreWaterIntakeRequest;
use App\Http\Resources\WaterIntakeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WaterIntakeController extends Controller
{
    public function __construct(
        private readonly StoreWaterIntakeAction $storeWaterIntakeAction,
        private readonly ListWaterIntakesAction $listWaterIntakesAction,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $intakes = $this->listWaterIntakesAction->execute($user, $request->query('date'));

        return WaterIntakeResource::collection($intakes)->response();
    }

    public function store(StoreWaterIntakeRequest $request): JsonResponse
    {
        $user = $request->user();

        $intake = $this->storeWaterIntakeAction->execute($user, $request->toData());

        return WaterIntakeResource::make($intake)
            ->response()
            ->setStatusCode(201);
    }
}



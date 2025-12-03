<?php

namespace App\Http\Controllers\Api;

use App\Actions\Water\GetDailyWaterIntakeStatsAction;
use App\Actions\Water\GetWeeklyWaterIntakeStatsAction;
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
        private readonly GetDailyWaterIntakeStatsAction $getDailyWaterIntakeStatsAction,
        private readonly GetWeeklyWaterIntakeStatsAction $getWeeklyWaterIntakeStatsAction,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $date = $request->query('date');

        $intakes = $this->listWaterIntakesAction->execute($user, $date);
        $stats = $this->getDailyWaterIntakeStatsAction->execute($user, $date);

        return response()->json([
            'data' => WaterIntakeResource::collection($intakes),
            'meta' => $stats,
        ]);
    }

    public function store(StoreWaterIntakeRequest $request): JsonResponse
    {
        $user = $request->user();

        $intake = $this->storeWaterIntakeAction->execute($user, $request->toData());

        return WaterIntakeResource::make($intake)
            ->response()
            ->setStatusCode(201);
    }

    public function weeklyStats(Request $request): JsonResponse
    {
        $user = $request->user();
        $startDate = $request->query('start_date');

        $stats = $this->getWeeklyWaterIntakeStatsAction->execute($user, $startDate);

        return response()->json($stats);
    }
}



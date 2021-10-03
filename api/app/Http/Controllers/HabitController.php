<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use HabitTracking\Application\HabitService;

class HabitController extends Controller
{
    public function __construct(
        private HabitService $service,
    ) {
    }

    public function index(): JsonResponse
    {
        $habits = $this->service->retrieveHabits();

        return response()->json($habits);
    }

    public function todayIndex(): JsonResponse
    {
        $habits = $this->service->retrieveHabitsForToday();

        return response()->json($habits);
    }

    public function start(Request $request): JsonResponse
    {
        $habit = $this->service->startHabit(
            $request->get('name'),
            $request->get('frequency'),
        );

        return response()->json($habit, JsonResponse::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $habit = $this->service->retrieveHabit($id);

        return response()->json($habit);
    }

    public function update(
        Request $request,
        string $id
    ): JsonResponse {

        $habit = $this->service->editHabit(
            $id,
            $request->get('name'),
            $request->get('frequency')
        );

        return response()->json($habit);
    }

    public function complete(string $id): JsonResponse
    {
        $habit = $this->service->markHabitAsComplete($id);

        return response()->json($habit);
    }

    public function incomplete(string $id): JsonResponse
    {
        $habit = $this->service->markHabitAsIncomplete($id);

        return response()->json($habit);
    }

    public function stop(string $id): JsonResponse
    {
        try {
            $this->service->stopHabit($id);
        } catch (\Exception $e) {
            return response()->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return response()->json();
    }
}

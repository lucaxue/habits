<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use HabitTracking\Application\HabitService;
use HabitTracking\Domain\Exceptions\HabitStoppedException;
use HabitTracking\Domain\Exceptions\HabitNotFoundException;
use HabitTracking\Domain\Exceptions\HabitAlreadyCompletedException;
use HabitTracking\Domain\Exceptions\HabitAlreadyIncompletedException;
use HabitTracking\Domain\Exceptions\HabitDoesNotBelongToAuthorException;

class HabitController extends Controller
{
    public function __construct(
        private HabitService $service,
        private AuthManager $auth
    ) {
    }

    public function index(): JsonResponse
    {
        $habits = $this->service->retrieveHabits($this->auth->id());

        return response()->json($habits);
    }

    public function todayIndex(): JsonResponse
    {
        $habits = $this->service->retrieveHabitsForToday($this->auth->id());

        return response()->json($habits);
    }

    public function start(Request $request): JsonResponse
    {
        $habit = $this->service->startHabit(
            $request->get('name'),
            $request->get('frequency'),
            $this->auth->id(),
        );

        return response()->json($habit, JsonResponse::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        try {
            $habit = $this->service->retrieveHabit($id, $this->auth->id());

        } catch (HabitNotFoundException $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthorException $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json($habit);
    }

    public function update(
        Request $request,
        string $id
    ): JsonResponse {

        try {
            $habit = $this->service->editHabit(
                $id,
                $request->get('name'),
                $request->get('frequency'),
                $this->auth->id()
            );
        } catch (HabitNotFoundException $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthorException $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json($habit);
    }

    public function complete(string $id): JsonResponse
    {
        try {
            $habit = $this->service->markHabitAsComplete($id, $this->auth->id());

        } catch (HabitNotFoundException $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthorException $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);

        } catch (HabitAlreadyCompletedException $e) {
            return response()->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return response()->json($habit);
    }

    public function incomplete(string $id): JsonResponse
    {
        try {
            $habit = $this->service->markHabitAsIncomplete($id, $this->auth->id());

        } catch (HabitNotFoundException $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthorException $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);

        } catch (HabitAlreadyIncompletedException $e) {
            return response()->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return response()->json($habit);
    }

    public function stop(string $id): JsonResponse
    {
        try {
            $this->service->stopHabit($id, $this->auth->id());

        } catch (HabitNotFoundException $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthorException $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);

        } catch (HabitStoppedException $e) {
            return response()->json(
                ['error' => ['message' => $e->getMessage()]],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return response()->json();
    }
}

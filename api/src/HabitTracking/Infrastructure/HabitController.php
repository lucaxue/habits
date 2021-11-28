<?php

namespace HabitTracking\Infrastructure;

use App\Http\Controllers\Controller;
use HabitTracking\Application\HabitService;
use HabitTracking\Presentation\HabitFinder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitController extends Controller
{
    public function index(
        HabitFinder $finder,
        Request $request,
    ) : JsonResponse {

        $filters = ['author' => Auth::id()];

        if ($request->has('today')) {
            $filters['today'] = true;
        }

        $habits = $finder->all($filters);

        return response()->json($habits);
    }

    public function start(
        HabitService $service,
        Request $request
    ) : JsonResponse {

        $habit = $service->startHabit(
            $request->get('name'),
            $request->get('frequency'),
            Auth::id(),
        );

        return response()->json($habit, JsonResponse::HTTP_CREATED);
    }

    public function show(
        HabitService $service,
        string $id,
    ) : JsonResponse {

        $habit = $service->retrieveHabit($id, Auth::id());

        return response()->json($habit);
    }

    public function update(
        Request $request,
        HabitService $service,
        string $id
    ) : JsonResponse {

        $habit = $service->editHabit(
            $id,
            $request->get('name'),
            $request->get('frequency'),
            Auth::id()
        );

        return response()->json($habit);
    }

    public function complete(
        HabitService $service,
        string $id,
    ) : JsonResponse {

        $habit = $service->markHabitAsComplete($id, Auth::id());

        return response()->json($habit);
    }

    public function incomplete(
        HabitService $service,
        string $id,
    ) : JsonResponse {

        $habit = $service->markHabitAsIncomplete($id, Auth::id());

        return response()->json($habit);
    }

    public function stop(
        HabitService $service,
        string $id
    ) : JsonResponse {

        $service->stopHabit($id, Auth::id());

        return response()->json();
    }
}

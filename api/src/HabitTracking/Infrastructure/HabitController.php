<?php

namespace HabitTracking\Infrastructure;

use App\Http\Controllers\Controller;
use HabitTracking\Application\HabitService;
use HabitTracking\Domain\Exceptions\HabitAlreadyCompleted;
use HabitTracking\Domain\Exceptions\HabitAlreadyIncompleted;
use HabitTracking\Domain\Exceptions\HabitAlreadyStopped;
use HabitTracking\Domain\Exceptions\HabitDoesNotBelongToAuthor;
use HabitTracking\Domain\Exceptions\HabitNotFound;
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

        try {
            $habit = $service->retrieveHabit($id, Auth::id());
        }
        catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json($habit);
    }

    public function update(
        Request $request,
        HabitService $service,
        string $id
    ) : JsonResponse {

        try {
            $habit = $service->editHabit(
                $id,
                $request->get('name'),
                $request->get('frequency'),
                Auth::id()
            );
        }
        catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json($habit);
    }

    public function complete(
        HabitService $service,
        string $id,
    ) : JsonResponse {

        try {
            $habit = $service->markHabitAsComplete($id, Auth::id());
        }
        catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }
        catch (HabitAlreadyCompleted $e) {
            return response()->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return response()->json($habit);
    }

    public function incomplete(
        HabitService $service,
        string $id,
    ) : JsonResponse {

        try {
            $habit = $service->markHabitAsIncomplete($id, Auth::id());
        }
        catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }
        catch (HabitAlreadyIncompleted $e) {
            return response()->json($e::class, JsonResponse::HTTP_BAD_REQUEST);
        }

        return response()->json($habit);
    }

    public function stop(
        HabitService $service,
        string $id
    ) : JsonResponse {

        try {
            $service->stopHabit($id, Auth::id());
        }
        catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }
        catch (HabitAlreadyStopped $e) {
            return response()->json(
                ['error' => ['message' => $e->getMessage()]],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return response()->json();
    }
}

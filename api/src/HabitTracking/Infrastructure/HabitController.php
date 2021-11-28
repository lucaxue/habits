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
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitController extends Controller
{
    public function __construct(
        private HabitService $service,
        private AuthManager $auth,
    ) {}

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

    public function start(Request $request) : JsonResponse
    {
        $habit = $this->service->startHabit(
            $request->get('name'),
            $request->get('frequency'),
            $this->auth->id(),
        );

        return response()->json($habit, JsonResponse::HTTP_CREATED);
    }

    public function show(string $id) : JsonResponse
    {
        try {
            $habit = $this->service->retrieveHabit($id, $this->auth->id());

        } catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json($habit);
    }

    public function update(
        Request $request,
        string $id
    ) : JsonResponse {

        try {
            $habit = $this->service->editHabit(
                $id,
                $request->get('name'),
                $request->get('frequency'),
                $this->auth->id()
            );
        } catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json($habit);
    }

    public function complete(string $id) : JsonResponse
    {
        try {
            $habit = $this->service->markHabitAsComplete($id, $this->auth->id());

        } catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);

        } catch (HabitAlreadyCompleted $e) {
            return response()->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return response()->json($habit);
    }

    public function incomplete(string $id) : JsonResponse
    {
        try {
            $habit = $this->service->markHabitAsIncomplete($id, $this->auth->id());

        } catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);

        } catch (HabitAlreadyIncompleted $e) {
            return response()->json($e::class, JsonResponse::HTTP_BAD_REQUEST);
        }

        return response()->json($habit);
    }

    public function stop(string $id) : JsonResponse
    {
        try {
            $this->service->stopHabit($id, $this->auth->id());

        } catch (HabitNotFound $e) {
            return response()->json(null, JsonResponse::HTTP_NOT_FOUND);

        } catch (HabitDoesNotBelongToAuthor $e) {
            return response()->json(null, JsonResponse::HTTP_UNAUTHORIZED);

        } catch (HabitAlreadyStopped $e) {
            return response()->json(
                ['error' => ['message' => $e->getMessage()]],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return response()->json();
    }
}

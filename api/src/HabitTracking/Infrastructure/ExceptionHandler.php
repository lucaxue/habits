<?php

namespace HabitTracking\Infrastructure;

use HabitTracking\Domain\Exceptions\HabitAlreadyCompleted;
use HabitTracking\Domain\Exceptions\HabitAlreadyIncompleted;
use HabitTracking\Domain\Exceptions\HabitAlreadyStopped;
use HabitTracking\Domain\Exceptions\HabitDoesNotBelongToAuthor;
use HabitTracking\Domain\Exceptions\HabitNotFound;
use Illuminate\Http\JsonResponse;

class ExceptionHandler
{
    /** @return \Closure[] */
    public function renderables() : array
    {
        return [
            fn (HabitNotFound $e) => response()->json($e::class, JsonResponse::HTTP_NOT_FOUND),
            fn (HabitDoesNotBelongToAuthor $e) => response()->json($e::class, JsonResponse::HTTP_UNAUTHORIZED),
            fn (HabitAlreadyCompleted $e) => response()->json($e::class, JsonResponse::HTTP_BAD_REQUEST),
            fn (HabitAlreadyIncompleted $e) => response()->json($e::class, JsonResponse::HTTP_BAD_REQUEST),
            fn (HabitAlreadyStopped $e) => response()->json($e::class, JsonResponse::HTTP_BAD_REQUEST),
        ];
    }
}

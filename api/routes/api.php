<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use HabitTracking\Infrastructure\HabitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('sanctum/token', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email', 'exists:users,email'],
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'password' => ['The provided password is incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Request $request) => $request->user());

    Route::prefix('habits')->group(function () {
        Route::get('today', [HabitController::class, 'todayIndex']);
        Route::get('', [HabitController::class, 'index']);
        Route::get('{id}', [HabitController::class, 'show']);
        Route::post('', [HabitController::class, 'start']);
        Route::put('{id}', [HabitController::class, 'update']);
        Route::put('{id}/complete', [HabitController::class, 'complete']);
        Route::put('{id}/incomplete', [HabitController::class, 'incomplete']);
        Route::delete('{id}', [HabitController::class, 'stop']);
    });
});

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

Route::get('{any}', fn (string $any) => $any);

Route::post('sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Request $request) => $request->user());

    Route::get('habits/today', [HabitController::class, 'todayIndex']);
    Route::get('habits', [HabitController::class, 'index']);
    Route::get('habits/{id}', [HabitController::class, 'show']);
    Route::post('habits', [HabitController::class, 'start']);
    Route::put('habits/{id}', [HabitController::class, 'update']);
    Route::put('habits/{id}/complete', [HabitController::class, 'complete']);
    Route::put('habits/{id}/incomplete', [HabitController::class, 'incomplete']);
    Route::delete('habits/{id}', [HabitController::class, 'stop']);
});

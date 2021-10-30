<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request) : JsonResponse
    {
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

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json(
            array_merge($user->toArray(),['token' => $token]),
            JsonResponse::HTTP_CREATED,
        );
    }

    public function register(Request $request) : JsonResponse
    {
        $validated = $this->validate($request, [
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required'],
            'password_confirmation' => ['required'],
        ]);

        if ($validated['password'] !== $validated['password_confirmation']) {
            throw ValidationException::withMessages([
                'password_confirmation' => ['Invalid password confirmation.'],
            ]);
        }

        $user = User::create($validated);

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json(
            array_merge($user->toArray(),['token' => $token]),
            JsonResponse::HTTP_CREATED,
        );
    }
}

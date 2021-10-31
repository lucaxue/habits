<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(fn () => User::factory()->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password')
]));

test('one can login and access protected routes', function () {
    $response = postJson('api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
            'device_name' => 'iPhone 13 Max',
        ])
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
            'token',
        ]);

    getJson('api/user', ['Authorization' => "Bearer {$response->getData()->token}"])
        ->assertOk();
});

test('one cannot login with unknown email')
    ->postJson('api/login', [
        'email' => 'unknown@email.com',
        'password' => 'password',
        'device_name' => 'iPhone 13 Max',
    ])
    ->assertJsonValidationErrors('email');

test('one cannot login with incorrect password')
    ->postJson('api/login', [
        'email' => 'john@example.com',
        'password' => 'incorrect-password',
        'device_name' => 'iPhone 13 Max',
    ])
    ->assertJsonValidationErrors('password');

test('one can register a new account and access protected routes', function () {
    $response = postJson('api/register', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'jane-likes-food-53',
            'password_confirmation' => 'jane-likes-food-53',
            'device_name' => 'iPhone 13 Max',
        ])
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
            'token',
        ]);

    getJson('api/user', ['Authorization' => "Bearer {$response->getData()->token}"])
        ->assertOk();
});

test('one cannot register with incorrect password confirmation')
    ->postJson('api/register', [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'password' => 'jane-likes-food-53',
        'password_confirmation' => 'wrong-confirmation',
        'device_name' => 'iPhone 13 Max',
    ])
    ->assertJsonValidationErrors('password');

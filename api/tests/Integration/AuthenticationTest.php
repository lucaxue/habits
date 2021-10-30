<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->john = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password')
    ]);
});

test('one can login and access protected routes', function () {
    $response = $this
        ->postJson('api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
            'device_name' => 'iPhone 13 Max',
        ])
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'created_at',
            'updated_at',
            'token',
        ])
        ->getData();

    $this
        ->getJson('api/user', ['Authorization' => "Bearer {$response->token}"])
        ->assertOk();
});

test('one cannot login with unknown email', function () {
    $this->postJson('api/login', [
            'email' => 'unknown@email.com',
            'password' => 'password',
            'device_name' => 'iPhone 13 Max',
        ])
        ->assertJsonValidationErrors('email');
});

test('one cannot login with incorrect password', function () {
    $this->postJson('api/login', [
            'email' => 'john@example.com',
            'password' => 'incorrect-password',
            'device_name' => 'iPhone 13 Max',
        ])
        ->assertJsonValidationErrors('password');
});

test('guests cannot access protected routes', function () {
    $this->getJson('api/user')
        ->assertUnauthorized();
});

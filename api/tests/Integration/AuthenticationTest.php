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
    $token = $this
        ->postJson('api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
            'device_name' => 'iPhone 13 Max',
        ])
        ->assertOk()
        ->getContent();

    $response = $this
        ->getJson('api/user', ['Authorization' => "Bearer {$token}"])
        ->assertOk();

    expect($response)
        ->getData()
        ->toMatchObject($this->john->toArray());
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

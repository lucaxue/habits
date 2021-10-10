<?php

namespace Tests;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function login(?Authenticatable $user = null)
    {
        $user ??= User::factory()->create();

        $this->actingAs($user);

        return $user;
    }
}

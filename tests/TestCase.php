<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function authenticate()
    {
        $user = \App\Models\User::factory()->create();

        $token = auth('api')->attempt([
            'email' => $user->email,
            'password' => 'password',
        ]);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_login_endpoint_returns_200()
    {
        $password = $this->faker->password;
        $user = User::factory()->create(['password' => $password]);

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertOk();
        $response->assertHeader('XSRF-TOKEN');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResetUserPasswordTest extends TestCase
{
    use RefreshDatabase;

    use RefreshDatabase;

    /**
     * Test password reset functionality.
     *
     * @return void
     */
    public function testPasswordReset()
    {
        $user = User::factory()->create();

        // Request password reset link
        $response = $this->postJson('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200); // Assert that the reset link request was successful

        $token = '';

        // Retrieve the password reset token from the notification
        $user->refresh();
        $notifications = $user->notifications;
        foreach ($notifications as $notification) {
            if ($notification->type === 'Illuminate\Auth\Notifications\ResetPassword') {
                $token = $notification->data['token'];
                break;
            }
        }

        $this->assertNotEmpty($token); // Assert that the reset token was retrieved successfully

        // Reset password with new password
        $response = $this->postJson('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200); // Assert that the password reset was successful
        $this->assertTrue(Password::broker()->validateToken($user, $token)); // Assert that the password reset token is valid

        // Attempt to log in with the new password
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'newpassword',
        ]);

        $response->assertStatus(200); // Assert that the login with the new password was successful
    }
}

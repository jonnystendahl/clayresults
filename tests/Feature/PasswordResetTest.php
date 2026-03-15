<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_request_a_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'shooter@example.test',
        ]);

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])
            ->assertSessionHas('status', __('We have emailed your password reset link.'));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_user_can_reset_their_password_with_a_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'shooter@example.test',
            'must_change_password' => true,
        ]);

        $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $token = null;

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', __('Your password has been reset.'));

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertFalse($user->fresh()->must_change_password);
    }
}
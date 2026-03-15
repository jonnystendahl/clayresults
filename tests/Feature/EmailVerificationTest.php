<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_a_verification_email_and_redirects_to_the_notice_page(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), [
            'name' => 'Anna Shooter',
            'email' => 'anna@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::query()->where('email', 'anna@example.test')->firstOrFail();

        $response
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'Please verify your email address using the link we just sent you.');

        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasVerifiedEmail());

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_is_redirected_to_email_verification_notice_from_member_routes(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($user)
            ->get(route('training-results.index'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Confirm your email address')
            ->assertSee('Send new verification email');
    }

    public function test_unverified_user_can_request_a_new_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHas('status', 'A new verification link has been sent to your email address.');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_user_can_verify_their_email_with_a_valid_signed_link(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHour(),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ],
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Your email address has been verified.');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_link_expires_after_one_hour(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHour(),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ],
        );

        $this->travel(61)->minutes();

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
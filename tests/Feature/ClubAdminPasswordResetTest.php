<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClubAdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_club_administrator_can_set_a_temporary_password_for_a_member(): void
    {
        $clubAdmin = User::factory()->create();
        $member = User::factory()->create();
        $club = Club::factory()->create(['name' => 'North Range']);

        $club->memberships()->create([
            'user_id' => $clubAdmin->id,
            'role' => 'Board member',
            'is_club_admin' => true,
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $club->memberships()->create([
            'user_id' => $member->id,
            'role' => 'Member',
            'is_club_admin' => false,
            'is_paid' => true,
            'joined_on' => '2026-01-02',
        ]);

        $this->actingAs($clubAdmin)
            ->post(route('clubs.members.password.store', [$club, $member]), [
                'password' => 'temporary-pass',
                'password_confirmation' => 'temporary-pass',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Temporary password saved for '.$member->name.'.');

        $this->assertTrue(Hash::check('temporary-pass', $member->fresh()->password));
        $this->assertTrue($member->fresh()->must_change_password);
    }

    public function test_member_with_temporary_password_is_redirected_to_required_password_change_after_login(): void
    {
        $member = User::factory()->create([
            'email' => 'member@example.test',
            'password' => bcrypt('temporary-pass'),
            'must_change_password' => true,
        ]);

        $this->post(route('login'), [
            'email' => $member->email,
            'password' => 'temporary-pass',
        ])->assertRedirect(route('password.change.edit'));
    }

    public function test_member_cannot_use_the_app_until_the_temporary_password_is_changed(): void
    {
        $member = User::factory()->create([
            'password' => bcrypt('temporary-pass'),
            'must_change_password' => true,
        ]);

        $this->actingAs($member)
            ->get(route('home'))
            ->assertRedirect(route('password.change.edit'));

        $this->actingAs($member)
            ->get(route('training-results.index'))
            ->assertRedirect(route('password.change.edit'));
    }

    public function test_member_can_set_a_new_password_after_logging_in_with_a_temporary_password(): void
    {
        $member = User::factory()->create([
            'password' => bcrypt('temporary-pass'),
            'must_change_password' => true,
        ]);

        $this->actingAs($member)
            ->put(route('password.change.update'), [
                'current_password' => 'temporary-pass',
                'password' => 'personal-pass',
                'password_confirmation' => 'personal-pass',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Your password has been updated.');

        $member->refresh();

        $this->assertFalse($member->must_change_password);
        $this->assertTrue(Hash::check('personal-pass', $member->password));
    }

    public function test_regular_member_cannot_set_temporary_password_for_another_member(): void
    {
        $member = User::factory()->create();
        $otherMember = User::factory()->create();
        $club = Club::factory()->create();

        $club->memberships()->create([
            'user_id' => $member->id,
            'role' => 'Member',
            'is_club_admin' => false,
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $club->memberships()->create([
            'user_id' => $otherMember->id,
            'role' => 'Member',
            'is_club_admin' => false,
            'is_paid' => true,
            'joined_on' => '2026-01-02',
        ]);

        $this->actingAs($member)
            ->post(route('clubs.members.password.store', [$club, $otherMember]), [
                'password' => 'temporary-pass',
                'password_confirmation' => 'temporary-pass',
            ])
            ->assertForbidden();
    }

    public function test_club_administrator_cannot_reset_password_for_user_outside_their_club(): void
    {
        $clubAdmin = User::factory()->create();
        $externalMember = User::factory()->create();
        $club = Club::factory()->create();

        $club->memberships()->create([
            'user_id' => $clubAdmin->id,
            'role' => 'Board member',
            'is_club_admin' => true,
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $this->actingAs($clubAdmin)
            ->post(route('clubs.members.password.store', [$club, $externalMember]), [
                'password' => 'temporary-pass',
                'password_confirmation' => 'temporary-pass',
            ])
            ->assertNotFound();
    }
}
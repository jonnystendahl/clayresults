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
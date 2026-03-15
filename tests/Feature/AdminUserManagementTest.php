<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_admin_can_update_a_member_and_grant_application_admin_access_from_a_club(): void
    {
        $admin = User::factory()->admin()->create();
        $managedUser = User::factory()->create([
            'is_admin' => false,
        ]);
        $club = Club::factory()->create();

        $club->memberships()->create([
            'member_id' => $admin->id,
            'role' => 'Chairperson',
            'is_club_admin' => true,
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $club->memberships()->create([
            'member_id' => $managedUser->id,
            'role' => 'Member',
            'is_club_admin' => false,
            'is_paid' => true,
            'joined_on' => '2026-01-02',
        ]);

        $this->actingAs($admin)
            ->put(route('club-admin.clubs.members.update', [$club, $managedUser]), [
                'name' => 'Updated Shooter',
                'email' => 'updated@example.test',
                'is_admin' => '1',
            ])
            ->assertRedirect(route('club-admin.clubs.members.edit', [$club, $managedUser]))
            ->assertSessionHas('status', 'Member updated.');

        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'name' => 'Updated Shooter',
            'email' => 'updated@example.test',
            'is_admin' => 1,
        ]);
    }

    public function test_last_application_administrator_cannot_remove_admin_access_from_the_last_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $club = Club::factory()->create();

        $club->memberships()->create([
            'member_id' => $admin->id,
            'role' => 'Chairperson',
            'is_club_admin' => true,
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $this->actingAs($admin)
            ->from(route('club-admin.clubs.members.edit', [$club, $admin]))
            ->put(route('club-admin.clubs.members.update', [$club, $admin]), [
                'name' => $admin->name,
                'email' => $admin->email,
                'is_admin' => '0',
            ])
            ->assertRedirect(route('club-admin.clubs.members.edit', [$club, $admin]))
            ->assertSessionHasErrors('is_admin');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_admin' => 1,
        ]);
    }

    public function test_club_administrator_can_update_a_member_profile_but_cannot_grant_application_admin_access(): void
    {
        $clubAdmin = User::factory()->create();
        $managedUser = User::factory()->create([
            'is_admin' => false,
        ]);
        $club = Club::factory()->create();

        $club->memberships()->create([
            'member_id' => $clubAdmin->id,
            'role' => 'Club admin',
            'is_club_admin' => true,
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $club->memberships()->create([
            'member_id' => $managedUser->id,
            'role' => 'Member',
            'is_club_admin' => false,
            'is_paid' => true,
            'joined_on' => '2026-01-02',
        ]);

        $this->actingAs($clubAdmin)
            ->put(route('club-admin.clubs.members.update', [$club, $managedUser]), [
                'name' => 'Renamed Club Member',
                'email' => 'renamed@example.test',
                'is_admin' => '1',
            ])
            ->assertRedirect(route('club-admin.clubs.members.edit', [$club, $managedUser]))
            ->assertSessionHas('status', 'Member updated.');

        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'name' => 'Renamed Club Member',
            'email' => 'renamed@example.test',
            'is_admin' => 0,
        ]);
    }
}
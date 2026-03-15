<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_when_opening_club_management(): void
    {
        $this->get(route('admin.clubs.index'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_users_cannot_open_club_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.clubs.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_and_update_a_club(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.clubs.store'), [
                'name' => 'Stockholm Lerduveklubb',
                'address' => 'Skjutbanevagen 10, Stockholm',
                'contact_person_name' => 'Anna Coach',
                'contact_person_email' => 'anna@example.test',
                'contact_person_phone' => '070-1234567',
                'note' => 'Main competition venue for the region.',
            ])
            ->assertRedirect();

        $club = Club::query()->where('name', 'Stockholm Lerduveklubb')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('club-admin.clubs.update', $club), [
                'name' => 'Stockholm Lerduveklubb',
                'address' => 'Skjutbanevagen 12, Stockholm',
                'contact_person_name' => 'Anna Coach',
                'contact_person_email' => 'anna@example.test',
                'contact_person_phone' => '070-9999999',
                'note' => 'Updated admin contact details.',
            ])
            ->assertRedirect(route('club-admin.clubs.edit', $club))
            ->assertSessionHas('status', 'Club updated.');

        $this->assertDatabaseHas('clubs', [
            'id' => $club->id,
            'address' => 'Skjutbanevagen 12, Stockholm',
            'contact_person_phone' => '070-9999999',
        ]);
    }

    public function test_admin_can_add_update_and_remove_a_club_membership(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create([
            'name' => 'Lisa Shooter',
            'email' => 'lisa@example.test',
        ]);
        $club = Club::factory()->create([
            'name' => 'Malaroarnas SK',
        ]);

        $this->actingAs($admin)
            ->post(route('club-admin.clubs.memberships.store', $club), [
                'name' => $member->name,
                'email' => $member->email,
                'role' => 'Board member',
                'is_club_admin' => '1',
                'is_paid' => '1',
                'joined_on' => '2026-01-15',
                'last_paid_on' => '2026-02-01',
                'ends_on' => '2026-12-31',
            ])
            ->assertRedirect(route('club-admin.clubs.edit', $club))
            ->assertSessionHas('status', 'Club membership added.');

        $membership = $club->memberships()->firstOrFail();
        $member->refresh();

        $this->assertDatabaseHas('club_memberships', [
            'club_id' => $club->id,
            'member_id' => $member->id,
            'role' => 'Board member',
            'is_club_admin' => 1,
            'is_paid' => 1,
        ]);

        $this->assertSame($club->id, $member->main_club_id);

        $this->actingAs($admin)
            ->put(route('club-admin.clubs.memberships.update', [$club, $membership]), [
                'role' => 'Official',
                'is_club_admin' => '0',
                'joined_on' => '2026-01-15',
                'last_paid_on' => '2026-03-01',
                'ends_on' => '2027-01-31',
            ])
            ->assertRedirect(route('club-admin.clubs.edit', $club))
            ->assertSessionHas('status', 'Club membership updated.');

        $membership->refresh();

        $this->assertDatabaseHas('club_memberships', [
            'id' => $membership->id,
            'role' => 'Official',
            'is_club_admin' => 0,
            'is_paid' => 0,
        ]);

        $this->assertSame('2026-03-01', $membership->last_paid_on?->toDateString());
        $this->assertSame('2027-01-31', $membership->ends_on?->toDateString());

        $this->actingAs($admin)
            ->delete(route('club-admin.clubs.memberships.destroy', [$club, $membership]))
            ->assertRedirect(route('club-admin.clubs.edit', $club))
            ->assertSessionHas('status', 'Club membership removed.');

        $this->assertDatabaseMissing('club_memberships', [
            'id' => $membership->id,
        ]);
    }

    public function test_deleting_a_club_removes_its_memberships(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $club = Club::factory()->create();

        $club->memberships()->create([
            'member_id' => $member->id,
            'role' => 'Member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
            'last_paid_on' => '2026-01-01',
            'ends_on' => '2026-12-31',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.clubs.destroy', $club))
            ->assertRedirect(route('admin.clubs.index'))
            ->assertSessionHas('status', 'Club deleted.');

        $this->assertDatabaseMissing('clubs', [
            'id' => $club->id,
        ]);

        $this->assertDatabaseMissing('club_memberships', [
            'club_id' => $club->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_club_administrator_can_manage_their_own_club_but_not_other_clubs(): void
    {
        $clubAdmin = User::factory()->create();
        $managedMember = User::factory()->create([
            'email' => 'managed@example.test',
        ]);
        $club = Club::factory()->create(['name' => 'Managed Club']);
        $otherClub = Club::factory()->create(['name' => 'Other Club']);

        $club->memberships()->create([
            'member_id' => $clubAdmin->id,
            'role' => 'Chairperson',
            'is_club_admin' => true,
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $club->memberships()->create([
            'member_id' => $managedMember->id,
            'role' => 'Member',
            'is_club_admin' => false,
            'is_paid' => true,
            'joined_on' => '2026-01-02',
        ]);

        $this->actingAs($clubAdmin)
            ->get(route('club-admin.clubs.edit', $club))
            ->assertOk()
            ->assertSee('Add member to '.$club->name)
            ->assertDontSee('Choose member');

        $this->actingAs($clubAdmin)
            ->put(route('club-admin.clubs.memberships.update', [$club, $club->memberships()->where('member_id', $managedMember->id)->firstOrFail()]), [
                'role' => 'Coach',
                'is_club_admin' => '0',
                'is_paid' => '1',
                'joined_on' => '2026-01-02',
                'last_paid_on' => '2026-03-01',
                'ends_on' => '2026-12-31',
            ])
            ->assertRedirect(route('club-admin.clubs.edit', $club));

        $this->assertDatabaseHas('club_memberships', [
            'club_id' => $club->id,
            'member_id' => $managedMember->id,
            'role' => 'Coach',
        ]);

        $this->actingAs($clubAdmin)
            ->get(route('club-admin.clubs.edit', $otherClub))
            ->assertForbidden();
    }
}
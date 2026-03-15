<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_when_opening_admin_users(): void
    {
        $this->get(route('admin.members.index'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_users_cannot_open_admin_user_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.members.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_the_user_directory(): void
    {
        $admin = User::factory()->admin()->create();
        $managedUser = User::factory()->create([
            'name' => 'Alex Shooter',
            'email' => 'alex@example.test',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.members.index'))
            ->assertOk()
            ->assertSee('Manage members')
            ->assertSee($managedUser->name)
            ->assertSee($managedUser->email);
    }

    public function test_admin_can_update_a_user_and_grant_admin_access(): void
    {
        $admin = User::factory()->admin()->create();
        $managedUser = User::factory()->create([
            'is_admin' => false,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.members.update', $managedUser), [
                'name' => 'Updated Shooter',
                'email' => 'updated@example.test',
                'is_admin' => '1',
            ])
            ->assertRedirect(route('admin.members.index'))
            ->assertSessionHas('status', 'Member updated.');

        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'name' => 'Updated Shooter',
            'email' => 'updated@example.test',
            'is_admin' => 1,
        ]);
    }

    public function test_last_administrator_cannot_remove_admin_access_from_the_last_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.members.edit', $admin))
            ->put(route('admin.members.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
            ])
            ->assertRedirect(route('admin.members.edit', $admin))
            ->assertSessionHasErrors('is_admin');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'is_admin' => 1,
        ]);
    }
}
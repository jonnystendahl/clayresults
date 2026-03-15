<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingResultManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_opening_results(): void
    {
        $this->get(route('training-results.index'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_register_a_training_result(): void
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();

        $club->memberships()->create([
            'member_id' => $user->id,
            'role' => 'Member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $user->update([
            'main_club_id' => $club->id,
        ]);

        $response = $this->actingAs($user)->post(route('training-results.store'), [
            'performed_on' => '2026-03-14',
            'discipline' => 'Nordisk Trap',
            'score' => 21,
            'note' => 'Good tempo through the middle stations.',
        ]);

        $response
            ->assertRedirect(route('training-results.index'))
            ->assertSessionHas('status', 'Training result saved.');

        $this->assertDatabaseHas('training_results', [
            'member_id' => $user->id,
            'club_id' => $club->id,
            'discipline' => 'Nordisk Trap',
            'score' => 21,
        ]);
    }

    public function test_user_can_only_edit_their_own_result(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $club = Club::factory()->create();

        $club->memberships()->create([
            'member_id' => $owner->id,
            'role' => 'Member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $owner->update([
            'main_club_id' => $club->id,
        ]);

        $result = $owner->trainingResults()->create([
            'club_id' => $club->id,
            'performed_on' => '2026-03-14',
            'discipline' => 'Olympisk Skeet',
            'score' => 20,
            'note' => 'Original note.',
        ]);

        $this->actingAs($otherUser)
            ->get(route('training-results.edit', $result))
            ->assertNotFound();

        $this->actingAs($owner)
            ->put(route('training-results.update', $result), [
                'performed_on' => '2026-03-15',
                'discipline' => 'Olympisk Skeet',
                'score' => 23,
                'note' => 'Much better second half.',
            ])
            ->assertRedirect(route('training-results.index'));

        $this->assertDatabaseHas('training_results', [
            'id' => $result->id,
            'score' => 23,
            'note' => 'Much better second half.',
        ]);
    }

    public function test_user_can_delete_their_result(): void
    {
        $user = User::factory()->create();
        $club = Club::factory()->create();

        $club->memberships()->create([
            'member_id' => $user->id,
            'role' => 'Member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);

        $user->update([
            'main_club_id' => $club->id,
        ]);

        $result = $user->trainingResults()->create([
            'club_id' => $club->id,
            'performed_on' => '2026-03-14',
            'discipline' => 'Automat Trap',
            'score' => 19,
            'note' => 'Wind picked up.',
        ]);

        $this->actingAs($user)
            ->delete(route('training-results.destroy', $result))
            ->assertRedirect(route('training-results.index'))
            ->assertSessionHas('status', 'Training result deleted.');

        $this->assertDatabaseMissing('training_results', [
            'id' => $result->id,
        ]);
    }
}
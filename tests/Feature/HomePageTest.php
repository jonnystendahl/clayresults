<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_homepage_shows_application_info_and_club_directory(): void
    {
        $clubOne = Club::factory()->create([
            'name' => 'Stockholm Lerduveklubb',
            'address' => 'Stockholm',
        ]);
        $clubTwo = Club::factory()->create([
            'name' => 'Malaroarnas SK',
            'address' => 'Ekero',
        ]);

        $clubOne->memberships()->create([
            'user_id' => User::factory()->create()->id,
            'role' => 'Member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);
        $clubTwo->memberships()->create([
            'user_id' => User::factory()->create()->id,
            'role' => 'Board member',
            'is_paid' => true,
            'joined_on' => '2026-01-10',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Club manager')
            ->assertSee('Stockholm Lerduveklubb')
            ->assertSee('Malaroarnas SK')
            ->assertSee('Stockholm')
            ->assertSee('Ekero')
            ->assertSee('Clubs in the system');
    }

    public function test_logged_in_user_sees_their_main_club_page_on_home(): void
    {
        $user = User::factory()->create();
        $mainClub = Club::factory()->create([
            'name' => 'Main Club',
            'address' => 'Uppsala',
        ]);
        $otherClub = Club::factory()->create([
            'name' => 'Second Club',
            'address' => 'Vasteras',
        ]);

        $mainClub->memberships()->create([
            'user_id' => $user->id,
            'role' => 'Official',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
            'last_paid_on' => '2026-02-01',
            'ends_on' => '2026-12-31',
        ]);
        $otherClub->memberships()->create([
            'user_id' => $user->id,
            'role' => 'Member',
            'is_paid' => false,
            'joined_on' => '2026-01-15',
        ]);

        $user->update([
            'main_club_id' => $mainClub->id,
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Main Club')
            ->assertSee('Membership details for Main Club')
            ->assertSee('Second Club')
            ->assertSee('Official');
    }

    public function test_user_can_switch_main_club_from_the_menu(): void
    {
        $user = User::factory()->create();
        $firstClub = Club::factory()->create([
            'name' => 'First Club',
        ]);
        $secondClub = Club::factory()->create([
            'name' => 'Second Club',
        ]);

        $firstClub->memberships()->create([
            'user_id' => $user->id,
            'role' => 'Member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);
        $secondClub->memberships()->create([
            'user_id' => $user->id,
            'role' => 'Board member',
            'is_paid' => true,
            'joined_on' => '2026-02-01',
        ]);

        $user->update([
            'main_club_id' => $firstClub->id,
        ]);

        $this->actingAs($user)
            ->post(route('clubs.main.update', $secondClub))
            ->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Second Club is now your main club.');

        $this->assertSame($secondClub->id, $user->fresh()->main_club_id);
    }
}
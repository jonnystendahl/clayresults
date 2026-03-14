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
            ->assertSee('Clubs in the system')
            ->assertSee(route('clubs.show', $clubOne), false);
    }

    public function test_guest_can_open_a_public_club_detail_page(): void
    {
        $club = Club::factory()->create([
            'name' => 'Stockholm Lerduveklubb',
            'address' => 'Stockholm',
            'contact_person_name' => 'Anna Coach',
            'contact_person_email' => 'anna@example.test',
        ]);

        $club->memberships()->create([
            'user_id' => User::factory()->create(['name' => 'Eva Board'])->id,
            'role' => 'Board member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);
        $club->memberships()->create([
            'user_id' => User::factory()->create(['name' => 'Nils Member'])->id,
            'role' => 'Member',
            'is_paid' => false,
            'joined_on' => '2026-01-02',
        ]);

        $this->get(route('clubs.show', $club))
            ->assertOk()
            ->assertSee('Stockholm Lerduveklubb')
            ->assertSee('Club news placeholder')
            ->assertSee('Upcoming events placeholder')
            ->assertSee('Board and officials placeholder')
            ->assertSee('Renewal guidance placeholder')
            ->assertSee('Eva Board')
            ->assertDontSee('Nils Member');
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
            ->assertSee('Official')
            ->assertSee('Club news placeholder')
            ->assertSee('Events and training placeholder')
            ->assertSee('Board and official roles placeholder')
            ->assertSee('Renewal and payment placeholder');
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
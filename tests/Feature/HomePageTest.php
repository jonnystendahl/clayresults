<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\ClubBoardMember;
use App\Models\ClubEvent;
use App\Models\ClubNewsPost;
use App\Models\ClubRenewalSetting;
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
            'member_id' => User::factory()->create()->id,
            'role' => 'Member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);
        $clubTwo->memberships()->create([
            'member_id' => User::factory()->create()->id,
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

        $club->newsPosts()->create([
            'title' => 'Spring opening',
            'excerpt' => 'Range opens for the season.',
            'body' => 'The range opens next weekend.',
            'published_at' => now()->subDay(),
        ]);
        $club->events()->create([
            'title' => 'Training night',
            'location' => 'Range A',
            'description' => 'Weekly trap training.',
            'starts_at' => now()->addWeek(),
            'published_at' => now()->subDay(),
        ]);
        $club->boardMembers()->create([
            'name' => 'Eva Board',
            'title' => 'Chairperson',
            'email' => 'eva@example.test',
            'is_public' => true,
            'sort_order' => 1,
        ]);
        $club->boardMembers()->create([
            'name' => 'Nils Member',
            'title' => 'Internal contact',
            'email' => 'nils@example.test',
            'is_public' => false,
            'sort_order' => 2,
        ]);
        $club->renewalSetting()->create([
            'season_label' => '2026',
            'title' => 'Renew your 2026 membership',
            'description' => 'Submit your request before the deadline.',
            'fee_amount' => 1200,
            'fee_currency' => 'SEK',
            'renewal_deadline' => '2026-03-31',
            'payment_details' => 'Pay by bankgiro 123-4567.',
            'is_open' => true,
        ]);

        $this->get(route('clubs.show', $club))
            ->assertOk()
            ->assertSee('Stockholm Lerduveklubb')
            ->assertSee('Club news')
            ->assertSee('Events and calendar')
            ->assertSee('Board information')
            ->assertSee('Membership renewal')
            ->assertSee(route('clubs.news', $club), false)
            ->assertSee(route('clubs.events', $club), false)
            ->assertSee(route('clubs.board', $club), false)
            ->assertSee(route('clubs.renewal', $club), false);

        $this->get(route('clubs.board', $club))
            ->assertOk()
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
            'member_id' => $user->id,
            'role' => 'Official',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
            'last_paid_on' => '2026-02-01',
            'ends_on' => '2026-12-31',
        ]);
        $otherClub->memberships()->create([
            'member_id' => $user->id,
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
            ->assertSee('Club news')
            ->assertSee('Events and training')
            ->assertSee('Board information')
            ->assertSee('Membership renewal')
            ->assertSee(route('clubs.news', $mainClub), false)
            ->assertSee(route('clubs.events', $mainClub), false)
            ->assertSee(route('clubs.board', $mainClub), false)
            ->assertSee(route('clubs.renewal', $mainClub), false);
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
            'member_id' => $user->id,
            'role' => 'Member',
            'is_paid' => true,
            'joined_on' => '2026-01-01',
        ]);
        $secondClub->memberships()->create([
            'member_id' => $user->id,
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
<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\ClubBoardMember;
use App\Models\ClubEvent;
use App\Models\ClubNewsPost;
use App\Models\ClubRenewalRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_dedicated_club_module_pages(): void
    {
        $club = Club::factory()->create(['name' => 'North Range Club']);

        $publishedPost = $club->newsPosts()->create([
            'title' => 'Opening day',
            'excerpt' => 'Season starts now.',
            'body' => 'Opening details.',
            'published_at' => now()->subDay(),
        ]);
        $club->newsPosts()->create([
            'title' => 'Draft post',
            'body' => 'Not yet public.',
        ]);

        $club->events()->create([
            'title' => 'Club championship',
            'location' => 'Main range',
            'description' => 'A full day competition.',
            'starts_at' => now()->addDays(10),
            'published_at' => now()->subDay(),
        ]);
        $club->events()->create([
            'title' => 'Internal planning',
            'description' => 'Not published yet.',
            'starts_at' => now()->addDays(30),
        ]);

        $club->boardMembers()->create([
            'name' => 'Anna Lead',
            'title' => 'Chairperson',
            'email' => 'anna@example.test',
            'is_public' => true,
        ]);
        $club->boardMembers()->create([
            'name' => 'Private Helper',
            'title' => 'Treasurer',
            'email' => 'private@example.test',
            'is_public' => false,
        ]);

        $club->renewalSetting()->create([
            'season_label' => '2026',
            'title' => 'Renew membership',
            'fee_amount' => 950,
            'fee_currency' => 'SEK',
            'renewal_deadline' => '2026-03-31',
            'payment_details' => 'Pay to account 1234.',
            'is_open' => true,
        ]);

        $this->get(route('clubs.news', $club))
            ->assertOk()
            ->assertSee($publishedPost->title)
            ->assertDontSee('Draft post');

        $this->get(route('clubs.events', $club))
            ->assertOk()
            ->assertSee('Club championship')
            ->assertDontSee('Internal planning');

        $this->get(route('clubs.board', $club))
            ->assertOk()
            ->assertSee('Anna Lead')
            ->assertDontSee('Private Helper');

        $this->get(route('clubs.renewal', $club))
            ->assertOk()
            ->assertSee('Renew membership')
            ->assertSee('950.00 SEK')
            ->assertSee('Log in');
    }

    public function test_admin_can_manage_club_modules(): void
    {
        $admin = User::factory()->admin()->create();
        $club = Club::factory()->create(['name' => 'South Trap Club']);

        $this->actingAs($admin)
            ->post(route('admin.clubs.news.store', $club), [
                'title' => 'News title',
                'excerpt' => 'Short note',
                'body' => 'Longer text',
                'published_at' => '2026-03-15 10:00',
            ])
            ->assertRedirect(route('admin.clubs.news.index', $club));

        $newsPost = ClubNewsPost::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.clubs.events.store', $club), [
                'title' => 'Spring event',
                'location' => 'Range B',
                'description' => 'Event description',
                'starts_at' => '2026-04-01 09:00',
                'ends_at' => '2026-04-01 15:00',
                'published_at' => '2026-03-20 08:00',
            ])
            ->assertRedirect(route('admin.clubs.events.index', $club));

        $event = ClubEvent::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.clubs.board.store', $club), [
                'name' => 'Eva Official',
                'title' => 'Secretary',
                'email' => 'eva@example.test',
                'phone' => '0701234567',
                'bio' => 'Handles communication.',
                'sort_order' => 1,
                'is_public' => '1',
            ])
            ->assertRedirect(route('admin.clubs.board.index', $club));

        $boardMember = ClubBoardMember::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.clubs.renewal.update', $club), [
                'season_label' => '2026',
                'title' => 'Renew 2026 membership',
                'description' => 'Submit before deadline.',
                'fee_amount' => '1100.00',
                'fee_currency' => 'SEK',
                'renewal_deadline' => '2026-03-31',
                'payment_details' => 'Swish 123 456 789',
                'is_open' => '1',
            ])
            ->assertRedirect(route('admin.clubs.renewal.edit', $club));

        $this->assertDatabaseHas('club_news_posts', [
            'id' => $newsPost->id,
            'club_id' => $club->id,
            'title' => 'News title',
        ]);

        $this->assertDatabaseHas('club_events', [
            'id' => $event->id,
            'club_id' => $club->id,
            'title' => 'Spring event',
        ]);

        $this->assertDatabaseHas('club_board_members', [
            'id' => $boardMember->id,
            'club_id' => $club->id,
            'name' => 'Eva Official',
            'is_public' => 1,
        ]);

        $this->assertDatabaseHas('club_renewal_settings', [
            'club_id' => $club->id,
            'season_label' => '2026',
            'title' => 'Renew 2026 membership',
            'is_open' => 1,
        ]);
    }

    public function test_member_can_submit_renewal_request_and_admin_can_review_it(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $club = Club::factory()->create(['name' => 'West Skeet']);

        $membership = $club->memberships()->create([
            'member_id' => $member->id,
            'role' => 'Member',
            'is_paid' => false,
            'joined_on' => '2026-01-01',
            'ends_on' => '2026-12-31',
        ]);

        $club->renewalSetting()->create([
            'season_label' => '2026',
            'title' => 'Renew your membership',
            'fee_amount' => 1000,
            'fee_currency' => 'SEK',
            'renewal_deadline' => '2026-03-31',
            'payment_details' => 'Pay by swish.',
            'is_open' => true,
        ]);

        $this->actingAs($member)
            ->post(route('clubs.renewal.store', $club), [
                'note' => 'Please renew my membership.',
            ])
            ->assertRedirect(route('clubs.renewal', $club))
            ->assertSessionHas('status', 'Membership renewal request submitted.');

        $renewalRequest = ClubRenewalRequest::query()->firstOrFail();

        $this->assertDatabaseHas('club_renewal_requests', [
            'id' => $renewalRequest->id,
            'club_id' => $club->id,
            'club_membership_id' => $membership->id,
            'member_id' => $member->id,
            'season_label' => '2026',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.clubs.renewal.requests.update', [$club, $renewalRequest]), [
                'status' => 'approved',
                'admin_note' => 'Payment received.',
            ])
            ->assertRedirect(route('admin.clubs.renewal.edit', $club))
            ->assertSessionHas('status', 'Renewal request updated.');

        $this->assertDatabaseHas('club_renewal_requests', [
            'id' => $renewalRequest->id,
            'status' => 'approved',
            'admin_note' => 'Payment received.',
        ]);
    }
}
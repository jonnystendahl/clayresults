<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClubTestDataSeeder extends Seeder
{
    /**
     * @var list<array{name: string, address: string, contact_person_name: string, contact_person_email: string, contact_person_phone: string, note: string}>
     */
    private const CLUBS = [
        [
            'name' => 'Demo North Clay Club',
            'address' => 'Granitvagen 12, Visby',
            'contact_person_name' => 'Anna Berg',
            'contact_person_email' => 'north@demo-clubs.test',
            'contact_person_phone' => '070-100 10 10',
            'note' => 'Sample club seeded for local development.',
        ],
        [
            'name' => 'Demo South Trap Club',
            'address' => 'Hamnleden 8, Malmo',
            'contact_person_name' => 'Johan Lind',
            'contact_person_email' => 'south@demo-clubs.test',
            'contact_person_phone' => '070-200 20 20',
            'note' => 'Sample club seeded for local development.',
        ],
        [
            'name' => 'Demo East Sporting Club',
            'address' => 'Tallstigen 5, Uppsala',
            'contact_person_name' => 'Sara Nystrom',
            'contact_person_email' => 'east@demo-clubs.test',
            'contact_person_phone' => '070-300 30 30',
            'note' => 'Sample club seeded for local development.',
        ],
        [
            'name' => 'Demo West Skeet Club',
            'address' => 'Skyttegatan 14, Goteborg',
            'contact_person_name' => 'Erik Holm',
            'contact_person_email' => 'west@demo-clubs.test',
            'contact_person_phone' => '070-400 40 40',
            'note' => 'Sample club seeded for local development.',
        ],
        [
            'name' => 'Demo Central Shotgun Club',
            'address' => 'Banvallen 21, Orebro',
            'contact_person_name' => 'Maria Ek',
            'contact_person_email' => 'central@demo-clubs.test',
            'contact_person_phone' => '070-500 50 50',
            'note' => 'Sample club seeded for local development.',
        ],
    ];

    /**
     * Seed sample clubs with random members.
     */
    public function run(): void
    {
        collect(self::CLUBS)->each(function (array $clubData): void {
            $club = Club::query()->updateOrCreate(
                ['name' => $clubData['name']],
                $clubData,
            );

            $clubSlug = Str::slug($club->name);
            $seedMembers = Member::query()
                ->where('email', 'like', sprintf('seed+%s-%%@example.com', $clubSlug))
                ->get();

            if ($seedMembers->isNotEmpty()) {
                $club->memberships()->whereIn('member_id', $seedMembers->modelKeys())->delete();

                Member::query()
                    ->whereKey($seedMembers->modelKeys())
                    ->whereDoesntHave('clubMemberships')
                    ->delete();
            }

            foreach (range(1, fake()->numberBetween(1, 5)) as $memberNumber) {
                $member = Member::query()->firstOrNew([
                    'email' => sprintf('seed+%s-%d@example.com', $clubSlug, $memberNumber),
                ]);

                $member->name = fake()->name();
                $member->email_verified_at = now();
                $member->password = 'password';
                $member->is_admin = false;
                $member->remember_token = Str::random(10);
                $member->main_club_id = $club->id;
                $member->save();

                $joinedOn = fake()->dateTimeBetween('-3 years', '-1 month');
                $isPaid = fake()->boolean(80);
                $lastPaidOn = $isPaid
                    ? fake()->dateTimeBetween($joinedOn, 'now')
                    : null;

                $club->memberships()->create([
                    'member_id' => $member->id,
                    'role' => fake()->randomElement(['member', 'trainer', 'board']),
                    'is_paid' => $isPaid,
                    'joined_on' => $joinedOn,
                    'last_paid_on' => $lastPaidOn,
                    'ends_on' => $isPaid ? fake()->dateTimeBetween('now', '+1 year') : null,
                ]);
            }
        });
    }
}
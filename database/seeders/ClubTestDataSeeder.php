<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\ClubMembership;
use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClubTestDataSeeder extends Seeder
{
    /**
     * @var list<array{name: string, address: string, contact_person_name: string, contact_person_email: string, contact_person_phone: string, note: string}>
     */
    private const CLUBS = [
        [
            'name' => 'Visby Lerduve Klubb',
            'address' => 'Hejdeby',
            'contact_person_name' => 'Jonny Stendahl',
            'contact_person_email' => 'skytte@skjulet.se',
            'contact_person_phone' => '0705364214',
            'note' => 'Board member',
        ],
        [
            'name' => 'Demo South Trap Club',
            'address' => 'Hamnleden 8, Malmo',
            'contact_person_name' => 'Johan Lind',
            'contact_person_email' => 'south@demo-clubs.test',
            'contact_person_phone' => '070-200 20 20',
            'note' => 'Sample club seeded for local development.',
        ],
    ];

    /**
     * @var array{name: string, email: string, email_verified_at: string, password: string, must_change_password: bool, is_admin: bool}
     */
    private const PRIMARY_MEMBER = [
        'name' => 'Jonny Stendahl',
        'email' => 'jonny.stendahl@skjulet.se',
        'email_verified_at' => '2026-03-09 09:35:19',
        'password' => 'password',
        'must_change_password' => false,
        'is_admin' => true,
    ];

    /**
     * @var array{role: string, is_club_admin: bool, is_paid: bool, joined_on: string, last_paid_on: string, ends_on: string}
     */
    private const PRIMARY_MEMBERSHIP = [
        'role' => 'Board member',
        'is_club_admin' => false,
        'is_paid' => true,
        'joined_on' => '2022-04-01',
        'last_paid_on' => '2026-03-03',
        'ends_on' => '2027-03-02',
    ];

    /**
     * Seed the trimmed local-development dataset.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            DB::table('training_results')->delete();
            DB::table('club_renewal_requests')->delete();
            DB::table('club_news_posts')->delete();
            DB::table('club_events')->delete();
            DB::table('club_board_members')->delete();
            DB::table('club_renewal_settings')->delete();

            $clubs = collect(self::CLUBS)
                ->mapWithKeys(function (array $clubData): array {
                    $club = Club::query()->updateOrCreate(
                        ['name' => $clubData['name']],
                        $clubData,
                    );

                    return [$club->name => $club];
                });

            Club::query()
                ->whereNotIn('name', $clubs->keys()->all())
                ->delete();

            $member = Member::query()->firstOrNew([
                'email' => self::PRIMARY_MEMBER['email'],
            ]);

            $member->fill([
                'name' => self::PRIMARY_MEMBER['name'],
                'email_verified_at' => Carbon::parse(self::PRIMARY_MEMBER['email_verified_at']),
                'must_change_password' => self::PRIMARY_MEMBER['must_change_password'],
                'is_admin' => self::PRIMARY_MEMBER['is_admin'],
            ]);

            if (! $member->exists) {
                $member->password = self::PRIMARY_MEMBER['password'];
            }

            $member->save();

            Member::query()
                ->where('email', '!=', self::PRIMARY_MEMBER['email'])
                ->delete();

            $visbyClub = $clubs->get('Visby Lerduve Klubb');

            $member->forceFill([
                'main_club_id' => $visbyClub?->id,
            ])->save();

            ClubMembership::query()
                ->where('member_id', '!=', $member->id)
                ->orWhere('club_id', '!=', $visbyClub?->id)
                ->delete();

            ClubMembership::query()->updateOrCreate(
                [
                    'club_id' => $visbyClub?->id,
                    'member_id' => $member->id,
                ],
                self::PRIMARY_MEMBERSHIP,
            );
        });
    }
}
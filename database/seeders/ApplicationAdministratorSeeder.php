<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ApplicationAdministratorSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::query()->firstOrNew([
            'email' => 'jonny.stendahl@skjulet.se',
        ]);

        $member->fill([
            'name' => 'Jonny Stendahl',
            'email_verified_at' => Carbon::parse('2026-03-09 09:35:19'),
            'must_change_password' => false,
            'is_admin' => true,
        ]);

        if (! $member->exists) {
            $member->password = 'password';
        }

        $member->save();
    }
}
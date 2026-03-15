<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class ApplicationAdministratorSeeder extends Seeder
{
    public function run(): void
    {
        Member::query()
            ->where('email', 'jonny.stendahl@skjulet.se')
            ->update(['is_admin' => true]);
    }
}
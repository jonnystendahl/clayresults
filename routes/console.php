<?php

use App\Models\Member;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('members:make-admin {email}', function (string $email): int {
    $member = Member::query()->where('email', $email)->first();

    if (! $member) {
        $this->error('No member found for that email address.');

        return self::FAILURE;
    }

    if ($member->isAdmin()) {
        $this->info($member->email.' is already an administrator.');

        return self::SUCCESS;
    }

    $member->forceFill(['is_admin' => true])->save();

    $this->info($member->email.' can now access the admin area.');

    return self::SUCCESS;
})->purpose('Grant administrator access to an existing member');

Artisan::command('users:make-admin {email}', function (string $email): int {
    return $this->call('members:make-admin', ['email' => $email]);
})->purpose('Legacy alias for granting administrator access to an existing member');

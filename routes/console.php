<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:make-admin {email}', function (string $email): int {
    $user = User::query()->where('email', $email)->first();

    if (! $user) {
        $this->error('No user found for that email address.');

        return self::FAILURE;
    }

    if ($user->isAdmin()) {
        $this->info($user->email.' is already an administrator.');

        return self::SUCCESS;
    }

    $user->forceFill(['is_admin' => true])->save();

    $this->info($user->email.' can now access the admin area.');

    return self::SUCCESS;
})->purpose('Grant administrator access to an existing user');

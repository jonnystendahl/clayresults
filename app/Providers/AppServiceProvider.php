<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $user = Auth::user();

            if ($user === null) {
                return;
            }

            $user->syncMainClub();
            $user->load([
                'mainClub',
                'clubs' => fn ($query) => $query->orderBy('name'),
            ]);

            $view->with('navigationUser', $user);
        });
    }
}

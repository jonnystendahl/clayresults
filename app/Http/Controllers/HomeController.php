<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->user() === null) {
            $clubs = Club::query()
                ->withCount('memberships')
                ->orderBy('name')
                ->get();

            return view('home.guest', [
                'clubs' => $clubs,
                'stats' => [
                    'clubs' => $clubs->count(),
                    'memberships' => (int) $clubs->sum('memberships_count'),
                ],
            ]);
        }

        $user = $request->user();

        $user->syncMainClub();
        $user->load([
            'clubs' => fn ($query) => $query->orderBy('name'),
            'mainClub',
        ]);

        $mainClub = $user->mainClub;
        $mainMembership = null;

        if ($mainClub !== null) {
            $mainClub->load([
                'memberships.user' => fn ($query) => $query->orderBy('name')->orderBy('email'),
                'newsPosts' => fn ($query) => $query->whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at'),
                'events' => fn ($query) => $query->whereNotNull('published_at')->where('published_at', '<=', now())->orderBy('starts_at'),
                'boardMembers' => fn ($query) => $query->where('is_public', true)->orderBy('sort_order')->orderBy('name'),
                'renewalSetting',
            ]);

            $mainMembership = $user->clubMemberships()
                ->where('club_id', $mainClub->id)
                ->first();
        }

        return view('home.authenticated', [
            'clubs' => $user->clubs,
            'mainClub' => $mainClub,
            'mainMembership' => $mainMembership,
            'menuClub' => $mainClub,
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\View\View;

class PublicClubController extends Controller
{
    public function show(Club $club): View
    {
        $club->load([
            'memberships.user' => fn ($query) => $query->orderBy('name')->orderBy('email'),
            'newsPosts' => fn ($query) => $query->whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at'),
            'events' => fn ($query) => $query->whereNotNull('published_at')->where('published_at', '<=', now())->orderBy('starts_at'),
            'boardMembers' => fn ($query) => $query->where('is_public', true)->orderBy('sort_order')->orderBy('name'),
            'renewalSetting',
        ]);

        return view('clubs.show', [
            'club' => $club,
            'menuClub' => $club,
        ]);
    }
}
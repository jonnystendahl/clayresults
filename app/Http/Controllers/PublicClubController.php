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
        ]);

        $leadershipMemberships = $club->memberships
            ->filter(fn ($membership) => in_array(mb_strtolower($membership->role), ['board member', 'official'], true))
            ->values();

        return view('clubs.show', [
            'club' => $club,
            'leadershipMemberships' => $leadershipMemberships,
            'menuClub' => $club,
        ]);
    }
}
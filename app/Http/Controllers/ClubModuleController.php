<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClubRenewalRequest;
use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubModuleController extends Controller
{
    public function news(Club $club): View
    {
        return view('clubs.modules.news', [
            'club' => $club,
            'menuClub' => $club,
            'newsPosts' => $club->newsPosts()
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->latest('id')
                ->get(),
        ]);
    }

    public function events(Club $club): View
    {
        return view('clubs.modules.events', [
            'club' => $club,
            'menuClub' => $club,
            'events' => $club->events()
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->orderBy('starts_at')
                ->get(),
        ]);
    }

    public function board(Club $club): View
    {
        return view('clubs.modules.board', [
            'club' => $club,
            'menuClub' => $club,
            'boardMembers' => $club->boardMembers()->where('is_public', true)->get(),
        ]);
    }

    public function renewal(Request $request, Club $club): View
    {
        $member = $request->user();
        $membership = null;
        $latestRenewalRequest = null;

        if ($member !== null && $member->canAccessClub($club)) {
            $membership = $member->clubMemberships()->where('club_id', $club->id)->first();
            $latestRenewalRequest = $membership?->renewalRequests()->latest('submitted_at')->first();
        }

        return view('clubs.modules.renewal', [
            'club' => $club,
            'menuClub' => $club,
            'renewalSetting' => $club->renewalSetting,
            'membership' => $membership,
            'latestRenewalRequest' => $latestRenewalRequest,
        ]);
    }

    public function storeRenewalRequest(StoreClubRenewalRequest $request, Club $club): RedirectResponse
    {
        $member = $request->user();
        $setting = $club->renewalSetting;

        abort_if($setting === null || ! $setting->is_open, 404);

        $membership = $member->clubMemberships()->where('club_id', $club->id)->firstOrFail();
        $seasonLabel = $setting->season_label ?: now()->format('Y');

        $existingRequest = $membership->renewalRequests()
            ->where('season_label', $seasonLabel)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingRequest) {
            return redirect()
                ->route('clubs.renewal', $club)
                ->withErrors(['note' => 'A renewal request for this season already exists.'])
                ->withInput();
        }

        $club->renewalRequests()->create([
            'club_membership_id' => $membership->id,
            'member_id' => $member->id,
            'season_label' => $seasonLabel,
            'status' => 'pending',
            'note' => $request->validated('note'),
            'submitted_at' => now(),
        ]);

        return redirect()->route('clubs.renewal', $club)->with('status', 'Membership renewal request submitted.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubMembershipRequest;
use App\Models\Club;
use App\Models\ClubMembership;
use Illuminate\Http\RedirectResponse;

class ClubMembershipController extends Controller
{
    public function store(ClubMembershipRequest $request, Club $club): RedirectResponse
    {
        $membership = $club->memberships()->create($request->validated());

        $membership->member->syncMainClub();

        return redirect()
            ->route('admin.clubs.edit', $club)
            ->with('status', 'Club membership added.');
    }

    public function update(ClubMembershipRequest $request, Club $club, ClubMembership $clubMembership): RedirectResponse
    {
        $membership = $this->clubMembership($club, $clubMembership);

        $membership->update($request->validated());

        return redirect()
            ->route('admin.clubs.edit', $club)
            ->with('status', 'Club membership updated.');
    }

    public function destroy(Club $club, ClubMembership $clubMembership): RedirectResponse
    {
        $membership = $this->clubMembership($club, $clubMembership);
        $member = $membership->member;

        $membership->delete();

        $member->syncMainClub();

        return redirect()
            ->route('admin.clubs.edit', $club)
            ->with('status', 'Club membership removed.');
    }

    private function clubMembership(Club $club, ClubMembership $clubMembership): ClubMembership
    {
        abort_unless($clubMembership->club->is($club), 404);

        return $clubMembership;
    }
}
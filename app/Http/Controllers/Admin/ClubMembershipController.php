<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesClubAdministration;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClubMembershipRequest;
use App\Models\Club;
use App\Models\ClubMembership;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ClubMembershipController extends Controller
{
    use AuthorizesClubAdministration;

    public function store(ClubMembershipRequest $request, Club $club): RedirectResponse
    {
        $validated = $request->validated();

        $member = Member::query()->firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Str::random(40),
                'must_change_password' => true,
            ],
        );

        $membership = $club->memberships()->create([
            'member_id' => $member->id,
            'role' => $validated['role'],
            'is_club_admin' => $validated['is_club_admin'],
            'is_paid' => $validated['is_paid'],
            'joined_on' => $validated['joined_on'],
            'last_paid_on' => $validated['last_paid_on'] ?? null,
            'ends_on' => $validated['ends_on'] ?? null,
        ]);

        $membership->member->syncMainClub();

        return redirect()
            ->route('club-admin.clubs.edit', $club)
            ->with('status', 'Club membership added.');
    }

    public function update(ClubMembershipRequest $request, Club $club, ClubMembership $clubMembership): RedirectResponse
    {
        $membership = $this->clubMembership($club, $clubMembership);

        $membership->update($request->validated());

        return redirect()
            ->route('club-admin.clubs.edit', $club)
            ->with('status', 'Club membership updated.');
    }

    public function destroy(Club $club, ClubMembership $clubMembership): RedirectResponse
    {
        $membership = $this->clubMembership($club, $clubMembership);
        $member = $membership->member;

        $membership->delete();

        $member->syncMainClub();

        return redirect()
            ->route('club-admin.clubs.edit', $club)
            ->with('status', 'Club membership removed.');
    }

    private function clubMembership(Club $club, ClubMembership $clubMembership): ClubMembership
    {
        abort_unless($clubMembership->club->is($club), 404);

        return $clubMembership;
    }
}
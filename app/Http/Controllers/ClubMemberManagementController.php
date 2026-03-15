<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesClubAdministration;
use App\Http\Requests\ClubMemberRequest;
use App\Models\Club;
use App\Models\ClubMembership;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubMemberManagementController extends Controller
{
    use AuthorizesClubAdministration;

    public function edit(Request $request, Club $club, Member $member): View
    {
        $this->ensureCanAdministerClub($request, $club);

        $membership = $this->membership($club, $member);
        $member->load('clubMemberships.club');

        return view('admin.users.edit', [
            'managedUser' => $member,
            'club' => $club,
            'membership' => $membership,
            'canEditAppAdministrator' => $request->user()?->isAdmin() ?? false,
        ]);
    }

    public function update(ClubMemberRequest $request, Club $club, Member $member): RedirectResponse
    {
        $this->membership($club, $member);

        $validated = $request->validated();

        if (($request->user()?->isAdmin() ?? false) && $member->isAdmin() && ! ($validated['is_admin'] ?? false) && Member::query()->where('is_admin', true)->count() === 1) {
            return back()
                ->withErrors(['is_admin' => 'At least one administrator must remain.'])
                ->withInput();
        }

        $member->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->user()?->isAdmin()) {
            $member->is_admin = $validated['is_admin'];
        }

        $member->save();

        return redirect()
            ->route('club-admin.clubs.members.edit', [$club, $member])
            ->with('status', 'Member updated.');
    }

    private function membership(Club $club, Member $member): ClubMembership
    {
        return $club->memberships()
            ->where('member_id', $member->id)
            ->firstOrFail();
    }
}
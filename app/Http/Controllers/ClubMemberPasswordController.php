<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClubMemberTemporaryPasswordRequest;
use App\Models\Club;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ClubMemberPasswordController extends Controller
{
    public function store(StoreClubMemberTemporaryPasswordRequest $request, Club $club, Member $member): RedirectResponse
    {
        abort_unless($club->memberships()->where('member_id', $member->id)->exists(), 404);

        $validated = $request->validated();

        $member->forceFill([
            'password' => $validated['password'],
            'must_change_password' => true,
            'remember_token' => Str::random(60),
        ])->save();

        Password::broker()->deleteToken($member);

        return redirect()
            ->route('home')
            ->with('status', 'Temporary password saved for '.$member->name.'.');
    }
}
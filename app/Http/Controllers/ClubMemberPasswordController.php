<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClubMemberTemporaryPasswordRequest;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ClubMemberPasswordController extends Controller
{
    public function store(StoreClubMemberTemporaryPasswordRequest $request, Club $club, User $user): RedirectResponse
    {
        abort_unless($club->memberships()->where('user_id', $user->id)->exists(), 404);

        $validated = $request->validated();

        $user->forceFill([
            'password' => $validated['password'],
            'remember_token' => Str::random(60),
        ])->save();

        Password::broker()->deleteToken($user);

        return redirect()
            ->route('home')
            ->with('status', 'Temporary password saved for '.$user->name.'.');
    }
}
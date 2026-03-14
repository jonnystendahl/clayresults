<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClubSelectionController extends Controller
{
    public function update(Request $request, Club $club): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null && $user->canAccessClub($club), 404);

        $user->setMainClub($club);

        return redirect()
            ->route('home')
            ->with('status', $club->name.' is now your main club.');
    }
}
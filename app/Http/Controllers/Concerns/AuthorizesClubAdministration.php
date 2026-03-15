<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Club;
use Illuminate\Http\Request;

trait AuthorizesClubAdministration
{
    protected function ensureCanAdministerClub(Request $request, Club $club): void
    {
        abort_unless($request->user()?->canAdministerClub($club), 403);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesClubAdministration;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClubBoardMemberRequest;
use App\Models\Club;
use App\Models\ClubBoardMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubBoardMemberController extends Controller
{
    use AuthorizesClubAdministration;

    public function index(Request $request, Club $club): View
    {
        $this->ensureCanAdministerClub($request, $club);

        return view('admin.clubs.board.index', [
            'club' => $club,
            'boardMembers' => $club->boardMembers()->get(),
        ]);
    }

    public function store(ClubBoardMemberRequest $request, Club $club): RedirectResponse
    {
        $club->boardMembers()->create($request->validated());

        return redirect()->route('club-admin.clubs.board.index', $club)->with('status', 'Board entry saved.');
    }

    public function update(ClubBoardMemberRequest $request, Club $club, ClubBoardMember $boardMember): RedirectResponse
    {
        $item = $this->boardMember($club, $boardMember);
        $item->update($request->validated());

        return redirect()->route('club-admin.clubs.board.index', $club)->with('status', 'Board entry updated.');
    }

    public function destroy(Club $club, ClubBoardMember $boardMember): RedirectResponse
    {
        $item = $this->boardMember($club, $boardMember);
        $item->delete();

        return redirect()->route('club-admin.clubs.board.index', $club)->with('status', 'Board entry deleted.');
    }

    private function boardMember(Club $club, ClubBoardMember $boardMember): ClubBoardMember
    {
        abort_unless($boardMember->club->is($club), 404);

        return $boardMember;
    }
}
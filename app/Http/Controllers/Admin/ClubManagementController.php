<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesClubAdministration;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClubRequest;
use App\Models\Club;
use App\Models\ClubMembership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubManagementController extends Controller
{
    use AuthorizesClubAdministration;

    public function index(): View
    {
        $clubs = Club::query()
            ->withCount('memberships')
            ->orderBy('name')
            ->get();

        return view('admin.clubs.index', [
            'clubs' => $clubs,
            'stats' => [
                'clubs' => $clubs->count(),
                'memberships' => (int) $clubs->sum('memberships_count'),
                'paidMemberships' => ClubMembership::query()->where('is_paid', true)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.clubs.create');
    }

    public function store(ClubRequest $request): RedirectResponse
    {
        $club = Club::query()->create($request->validated());

        return redirect()
            ->route('club-admin.clubs.edit', $club)
            ->with('status', 'Club created.');
    }

    public function edit(Request $request, Club $club): View
    {
        $this->ensureCanAdministerClub($request, $club);

        $club->load([
            'memberships.member' => fn ($query) => $query->orderBy('name')->orderBy('email'),
        ]);

        return view('admin.clubs.edit', [
            'club' => $club,
            'memberships' => $club->memberships->sortBy(fn ($membership) => $membership->member->name)->values(),
            'canDeleteClub' => $request->user()?->isAdmin() ?? false,
        ]);
    }

    public function update(ClubRequest $request, Club $club): RedirectResponse
    {
        $club->update($request->validated());

        return redirect()
            ->route('club-admin.clubs.edit', $club)
            ->with('status', 'Club updated.');
    }

    public function destroy(Club $club): RedirectResponse
    {
        $club->delete();

        return redirect()
            ->route('admin.clubs.index')
            ->with('status', 'Club deleted.');
    }
}
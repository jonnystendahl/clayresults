<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubRequest;
use App\Models\Club;
use App\Models\ClubMembership;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClubManagementController extends Controller
{
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
            ->route('admin.clubs.edit', $club)
            ->with('status', 'Club created.');
    }

    public function edit(Club $club): View
    {
        $club->load([
            'memberships.user' => fn ($query) => $query->orderBy('name')->orderBy('email'),
        ]);

        return view('admin.clubs.edit', [
            'club' => $club,
            'memberships' => $club->memberships->sortBy(fn ($membership) => $membership->user->name)->values(),
            'users' => User::query()->orderBy('name')->orderBy('email')->get(),
        ]);
    }

    public function update(ClubRequest $request, Club $club): RedirectResponse
    {
        $club->update($request->validated());

        return redirect()
            ->route('admin.clubs.edit', $club)
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
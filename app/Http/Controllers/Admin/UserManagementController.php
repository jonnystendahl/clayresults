<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = Member::query()
            ->orderBy('name')
            ->orderBy('email')
            ->get();

        return view('admin.users.index', [
            'users' => $users,
            'stats' => [
                'total' => $users->count(),
                'admins' => $users->where('is_admin', true)->count(),
                'members' => $users->where('is_admin', false)->count(),
            ],
        ]);
    }

    public function edit(Member $member): View
    {
        $member->load('clubMemberships.club');

        return view('admin.users.edit', [
            'managedUser' => $member,
        ]);
    }

    public function update(AdminUserRequest $request, Member $member): RedirectResponse
    {
        $validated = $request->validated();

        if ($member->isAdmin() && ! $validated['is_admin'] && Member::query()->where('is_admin', true)->count() === 1) {
            return back()
                ->withErrors(['is_admin' => 'At least one administrator must remain.'])
                ->withInput();
        }

        $member->update($validated);

        return redirect()
            ->route('admin.members.index')
            ->with('status', 'Member updated.');
    }
}
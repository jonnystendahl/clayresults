<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()
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

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'managedUser' => $user,
        ]);
    }

    public function update(AdminUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if ($user->isAdmin() && ! $validated['is_admin'] && User::query()->where('is_admin', true)->count() === 1) {
            return back()
                ->withErrors(['is_admin' => 'At least one administrator must remain.'])
                ->withInput();
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated.');
    }
}
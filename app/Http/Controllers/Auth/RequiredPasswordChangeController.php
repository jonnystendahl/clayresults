<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeRequiredPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RequiredPasswordChangeController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $user = request()->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $user->must_change_password) {
            return redirect()->route('home');
        }

        return view('auth/change-password-required');
    }

    public function update(ChangeRequiredPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'password' => Hash::make($request->string('password')->toString()),
            'must_change_password' => false,
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('home')
            ->with('status', 'Your password has been updated.');
    }
}
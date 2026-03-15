<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login', [
            'loginTitle' => 'Admin Login | KlubbManager',
            'loginEyebrow' => 'Administration',
            'loginHeading' => 'Log in to the administrator area',
            'loginDescription' => 'Use an administrator account to manage clubs, members, and application settings.',
            'loginAction' => route('admin.login.store'),
            'loginButtonLabel' => 'Log in as administrator',
            'alternateLoginUrl' => route('login'),
            'alternateLoginLabel' => 'Member login',
            'showRegisterLink' => false,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        if (! $request->user()?->isAdmin()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This account does not have administrator access.',
            ]);
        }

        if ($request->user()?->must_change_password) {
            return redirect()->route('password.change.edit');
        }

        return redirect()->intended(route('admin.clubs.index'));
    }
}
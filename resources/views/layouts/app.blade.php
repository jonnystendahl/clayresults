<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'KlubbManager') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @php
            $navigationUser = $navigationUser ?? auth()->user();
            $isUnverifiedUser = $navigationUser?->hasVerifiedEmail() === false;
            $requiresPasswordChange = $navigationUser?->must_change_password === true;
            $navigationClubs = ($isUnverifiedUser || $requiresPasswordChange) ? collect() : ($navigationUser?->clubs ?? collect());
            $currentMainClub = ($isUnverifiedUser || $requiresPasswordChange) ? null : $navigationUser?->mainClub;
            $menuClub = $menuClub ?? $currentMainClub ?? null;
        @endphp

        <nav class="navbar navbar-expand-lg sticky-top navbar-blur">
            <div class="container py-2">
                <a class="navbar-brand d-flex align-items-center gap-3 fw-semibold" href="{{ route('home') }}">
                    <span class="brand-mark">KM</span>
                    <span>KlubbManager</span>
                </a>

                @auth
                    <div class="d-flex align-items-center gap-2 gap-lg-3 ms-auto flex-wrap justify-content-end">
                        @if (! $isUnverifiedUser && $navigationClubs->isNotEmpty())
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $currentMainClub?->name ?? 'Choose club' }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 club-switcher-menu">
                                    @foreach ($navigationClubs as $club)
                                        <li>
                                            <form method="POST" action="{{ route('clubs.main.update', $club) }}">
                                                @csrf
                                                <button class="dropdown-item d-flex justify-content-between align-items-center gap-3" type="submit">
                                                    <span>{{ $club->name }}</span>
                                                    @if ($currentMainClub?->is($club))
                                                        <span class="badge text-bg-success">Main</span>
                                                    @endif
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($requiresPasswordChange)
                            <a class="btn btn-outline-primary" href="{{ route('password.change.edit') }}">Change password</a>
                        @elseif ($isUnverifiedUser)
                            <a class="btn btn-outline-primary" href="{{ route('verification.notice') }}">Verify email</a>
                        @else
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Menu
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('home') }}">Home</a></li>
                                    <li><a class="dropdown-item" href="{{ route('training-results.index') }}">Results</a></li>
                                    @if ($menuClub !== null)
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('clubs.show', $menuClub) }}">Public club page</a></li>
                                        <li><a class="dropdown-item" href="{{ route('clubs.news', $menuClub) }}">Club news</a></li>
                                        <li><a class="dropdown-item" href="{{ route('clubs.events', $menuClub) }}">Events</a></li>
                                        <li><a class="dropdown-item" href="{{ route('clubs.board', $menuClub) }}">Board information</a></li>
                                        <li><a class="dropdown-item" href="{{ route('clubs.renewal', $menuClub) }}">Membership renewal</a></li>
                                    @endif
                                    @if ($navigationUser?->isAdmin())
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.members.index') }}">Manage members</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.clubs.index') }}">Manage clubs</a></li>
                                    @endif
                                    @if (app()->environment('local') && Route::has('dev.mail.index'))
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('dev.mail.index') }}">Development mail inbox</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <span class="text-secondary d-none d-md-inline">{{ $navigationUser?->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-primary" type="submit">Log out</button>
                        </form>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-2 gap-lg-3 ms-auto flex-wrap justify-content-end">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Menu
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item" href="{{ route('home') }}">Home</a></li>
                                <li><a class="dropdown-item" href="{{ route('home') }}#club-directory">Clubs in system</a></li>
                                <li><a class="dropdown-item" href="{{ route('home') }}#app-overview">About the app</a></li>
                                @if ($menuClub !== null)
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('clubs.show', $menuClub) }}">Club overview</a></li>
                                    <li><a class="dropdown-item" href="{{ route('clubs.news', $menuClub) }}">Club news</a></li>
                                    <li><a class="dropdown-item" href="{{ route('clubs.events', $menuClub) }}">Events</a></li>
                                    <li><a class="dropdown-item" href="{{ route('clubs.board', $menuClub) }}">Board information</a></li>
                                    <li><a class="dropdown-item" href="{{ route('clubs.renewal', $menuClub) }}">Membership renewal</a></li>
                                @endif
                                @if (app()->environment('local') && Route::has('dev.mail.index'))
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('dev.mail.index') }}">Development mail inbox</a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('register') }}">Create account</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.login') }}">Administrator login</a></li>
                            </ul>
                        </div>
                        <a class="btn btn-primary" href="{{ route('login') }}">Log in</a>
                    </div>
                @endauth
            </div>
        </nav>

        <main class="py-4 py-lg-5">
            <div class="container">
                @if (session('status'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </body>
</html>
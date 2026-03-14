<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'ClayResults') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-lg sticky-top navbar-blur">
            <div class="container py-2">
                <a class="navbar-brand d-flex align-items-center gap-3 fw-semibold" href="{{ route('home') }}">
                    <span class="brand-mark">CR</span>
                    <span>ClayResults</span>
                </a>

                @auth
                    <div class="d-flex align-items-center gap-2 gap-lg-3 ms-auto flex-wrap justify-content-end">
                        <a class="btn btn-outline-primary" href="{{ route('training-results.index') }}">Results</a>
                        @if (auth()->user()->isAdmin())
                            <a class="btn btn-outline-primary" href="{{ route('admin.users.index') }}">Users</a>
                            <a class="btn btn-outline-primary" href="{{ route('admin.clubs.index') }}">Clubs</a>
                        @endif
                        <span class="text-secondary d-none d-md-inline">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-primary" type="submit">Log out</button>
                        </form>
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
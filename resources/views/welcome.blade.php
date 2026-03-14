<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'ClayResults') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell py-4 py-lg-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4 mb-lg-5">
                    <div class="d-flex align-items-center gap-3">
                        <span class="brand-mark">CR</span>
                        <div>
                            <div class="section-label mb-1">Clay shooting logbook</div>
                            <div class="fw-semibold">Track every round, improve every week</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @auth
                            <a class="btn btn-outline-primary" href="{{ route('training-results.index') }}">Open results</a>
                        @else
                            <a class="btn btn-outline-primary" href="{{ route('login') }}">Log in</a>
                            <a class="btn btn-primary" href="{{ route('register') }}">Create account</a>
                        @endauth
                    </div>
                </div>

                <section class="hero-panel">
                    <div class="row g-0">
                        <div class="col-lg-7 p-4 p-md-5 p-xl-6">
                            <span class="section-label">Built for training days</span>
                            <h1 class="display-4 fw-bold mt-3 mb-4 display-balance">A clean multi-user scorebook for trap and skeet training.</h1>
                            <p class="lead text-secondary mb-4">
                                Register your sessions, pick the discipline, save your score, and keep notes from every round.
                                Each shooter only sees and manages their own history.
                            </p>

                            <div class="d-flex flex-wrap gap-2 mb-4 mb-lg-5">
                                <span class="discipline-chip">Nordisk Trap</span>
                                <span class="discipline-chip">Automat Trap</span>
                                <span class="discipline-chip">Olympisk Skeet</span>
                                <span class="discipline-chip">Nationell Skeet</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6 col-xl-4">
                                    <div class="stats-card p-4 h-100">
                                        <div class="section-label mb-2">What users log</div>
                                        <p class="mb-0 text-secondary">Date, discipline, score, and notes from each training round.</p>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-4">
                                    <div class="stats-card p-4 h-100">
                                        <div class="section-label mb-2">Built for clubs</div>
                                        <p class="mb-0 text-secondary">Every account gets a separate result history, so the app works for many shooters.</p>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="stats-card p-4 h-100">
                                        <div class="section-label mb-2">Ready now</div>
                                        <p class="mb-0 text-secondary">Laravel backend, MySQL-compatible schema, Bootstrap 5 interface.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5 hero-band p-4 p-md-5 d-flex flex-column justify-content-between">
                            <div>
                                <div class="section-label text-warning mb-3">Inside the app</div>
                                <h2 class="h1 fw-bold mb-3">One place for every practice result.</h2>
                                <p class="text-white-50 mb-0">
                                    Sign up, record your series, edit mistakes later, and keep an organized archive of your training.
                                </p>
                            </div>

                            <div class="mt-4 mt-lg-5">
                                <div class="result-card bg-white text-dark p-4 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="section-label">Latest session example</div>
                                            <div class="fw-semibold fs-5">Olympisk Skeet</div>
                                        </div>
                                        <span class="result-score">22</span>
                                    </div>
                                    <div class="text-secondary small">14 March 2026</div>
                                    <p class="mb-0 mt-3 text-secondary">Strong finish. Better tempo on the doubles from station four onward.</p>
                                </div>

                                @guest
                                    <a class="btn btn-light btn-lg w-100" href="{{ route('register') }}">Start logging your results</a>
                                @else
                                    <a class="btn btn-light btn-lg w-100" href="{{ route('training-results.index') }}">Go to your results</a>
                                @endguest
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
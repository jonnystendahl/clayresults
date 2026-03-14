@php use Illuminate\Support\Str; @endphp

@extends('layouts.app', ['title' => 'Club Manager | ClayResults'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start mb-4 mb-lg-5" id="app-overview">
        <div class="col-xl-7">
            <div class="hero-panel h-100">
                <div class="row g-0 h-100">
                    <div class="col-lg-8 p-4 p-md-5 p-xl-6">
                        <span class="section-label">Club manager</span>
                        <h1 class="display-4 fw-bold mt-3 mb-4 display-balance">A shared home for clubs, members, training results, and the workflows around them.</h1>
                        <p class="lead text-secondary mb-4">
                            ClayResults is moving toward a broader club platform. This start page now focuses on clubs first,
                            with room for member administration, result tracking, payments, and club communication as the application grows.
                        </p>

                        <div class="d-flex flex-wrap gap-2 mb-4 mb-lg-5">
                            <span class="discipline-chip">Club directory</span>
                            <span class="discipline-chip">Membership tracking</span>
                            <span class="discipline-chip">Main club overview</span>
                            <span class="discipline-chip">Training results</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="stats-card p-4 h-100">
                                    <div class="section-label mb-2">Clubs in system</div>
                                    <div class="stats-value">{{ $stats['clubs'] }}</div>
                                    <div class="text-secondary">Registered clubs currently visible on the platform.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="stats-card p-4 h-100">
                                    <div class="section-label mb-2">Tracked memberships</div>
                                    <div class="stats-value">{{ $stats['memberships'] }}</div>
                                    <div class="text-secondary">Club memberships currently connected to user accounts.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 hero-band p-4 p-md-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="section-label text-warning mb-3">What comes next</div>
                            <h2 class="h2 fw-bold mb-3">Start with clubs, grow into the full shooting community workflow.</h2>
                            <p class="text-white-50 mb-0">
                                This section is intentionally generic so you can shape the story later around clubs, members,
                                officials, boards, events, payments, and training progress.
                            </p>
                        </div>

                        <div class="mt-4 mt-lg-5 vstack gap-3">
                            <div class="result-card bg-white text-dark p-4">
                                <div class="section-label mb-2">Suggested menu sections</div>
                                <div class="small text-secondary">Home, Clubs, Memberships, Results, Events, News, Contact.</div>
                            </div>
                            <a class="btn btn-light btn-lg w-100" href="{{ route('login') }}">Log in to your club</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5" id="future-sections">
            <div class="content-panel p-4 p-lg-5 h-100">
                <div class="section-label mb-2">About this application</div>
                <h2 class="h3 fw-bold mb-3">A generic intro section you can keep expanding later</h2>
                <p class="text-secondary mb-4">
                    For now this page introduces the application at a high level instead of only talking about results.
                    You can later replace this copy with club-specific messaging, screenshots, onboarding details, or links.
                </p>

                <div class="vstack gap-3">
                    <div class="result-card p-4">
                        <div class="fw-semibold mb-2">Membership administration</div>
                        <div class="text-secondary">Track which users belong to which clubs, their roles, paid status, and membership dates.</div>
                    </div>
                    <div class="result-card p-4">
                        <div class="fw-semibold mb-2">Club-first navigation</div>
                        <div class="text-secondary">Signed-in users can land on their main club and later switch to other clubs from the top menu.</div>
                    </div>
                    <div class="result-card p-4">
                        <div class="fw-semibold mb-2">Room to grow</div>
                        <div class="text-secondary">Results, member communication, events, and governance features can all fit under the same club structure.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="club-directory">
        <div class="content-panel p-4 p-lg-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <div class="section-label mb-2">Clubs in the system</div>
                    <h2 class="h2 fw-bold mb-0">Current club overview</h2>
                </div>
                <div class="text-secondary">Each card shows the club location and current member count.</div>
            </div>

            @if ($clubs->isEmpty())
                <div class="result-card p-4 p-lg-5 text-center">
                    <h3 class="h4 fw-semibold mb-2">No clubs available yet</h3>
                    <p class="text-secondary mb-0">Add the first club in the admin area to show it here.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($clubs as $club)
                        <div class="col-md-6 col-xl-4">
                            <div class="club-directory-card p-4 h-100">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <div class="section-label mb-2">Club</div>
                                        <h3 class="h4 fw-bold mb-0">{{ $club->name }}</h3>
                                    </div>
                                    <span class="result-score">{{ $club->memberships_count }}</span>
                                </div>

                                <div class="text-secondary mb-2">
                                    <span class="fw-semibold text-dark">Location:</span>
                                    {{ $club->address ?: 'Address will be added later.' }}
                                </div>

                                <div class="small text-secondary">{{ $club->memberships_count }} {{ Str::plural('member', $club->memberships_count) }} tracked in this club.</div>

                                <div class="mt-4 d-flex justify-content-end">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('clubs.show', $club) }}">View club</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
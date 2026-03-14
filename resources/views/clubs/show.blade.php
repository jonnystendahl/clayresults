@extends('layouts.app', ['title' => $club->name.' | ClayResults'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start mb-4 mb-lg-5">
        <div class="col-xl-4">
            <div class="content-panel p-4 p-lg-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">Public club page</div>
                <h1 class="h2 fw-bold mb-3">{{ $club->name }}</h1>
                <p class="text-secondary mb-4">This page gives guests a public overview of the club. It can grow into a richer landing page with events, news, board information, and membership guidance.</p>

                <div class="vstack gap-3 mb-4">
                    <div class="result-card p-4">
                        <div class="section-label mb-2">Location</div>
                        <div class="fw-semibold">{{ $club->address ?: 'Address not set yet.' }}</div>
                    </div>
                    <div class="result-card p-4">
                        <div class="section-label mb-2">Contact person</div>
                        <div class="fw-semibold">{{ $club->contact_person_name ?: 'No contact person yet' }}</div>
                        @if ($club->contact_person_email)
                            <div class="text-secondary small mt-2">{{ $club->contact_person_email }}</div>
                        @endif
                        @if ($club->contact_person_phone)
                            <div class="text-secondary small">{{ $club->contact_person_phone }}</div>
                        @endif
                    </div>
                </div>

                <a class="btn btn-primary w-100" href="{{ route('login') }}">Log in to this club</a>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row g-3 mb-4">
                <div class="col-md-4"><div class="stats-card p-4 h-100"><div class="section-label mb-2">Members</div><div class="stats-value">{{ $club->memberships->count() }}</div><div class="text-secondary">Tracked memberships connected to this club</div></div></div>
                <div class="col-md-4"><div class="stats-card p-4 h-100"><div class="section-label mb-2">News posts</div><div class="stats-value">{{ $club->newsPosts->count() }}</div><div class="text-secondary">Published club news items</div></div></div>
                <div class="col-md-4"><div class="stats-card p-4 h-100"><div class="section-label mb-2">Events</div><div class="stats-value">{{ $club->events->count() }}</div><div class="text-secondary">Published upcoming and archived events</div></div></div>
            </div>

            <div class="row g-3">
                <div class="col-md-6"><a class="content-panel p-4 p-lg-5 h-100 d-block text-decoration-none text-reset" href="{{ route('clubs.news', $club) }}"><div class="section-label mb-2">News</div><h2 class="h3 fw-bold mb-2">Club news</h2><p class="text-secondary mb-0">Read announcements, updates, and general communication from {{ $club->name }}.</p></a></div>
                <div class="col-md-6"><a class="content-panel p-4 p-lg-5 h-100 d-block text-decoration-none text-reset" href="{{ route('clubs.events', $club) }}"><div class="section-label mb-2">Events</div><h2 class="h3 fw-bold mb-2">Events and calendar</h2><p class="text-secondary mb-0">See competitions, training sessions, workdays, and other scheduled activity.</p></a></div>
                <div class="col-md-6"><a class="content-panel p-4 p-lg-5 h-100 d-block text-decoration-none text-reset" href="{{ route('clubs.board', $club) }}"><div class="section-label mb-2">Board</div><h2 class="h3 fw-bold mb-2">Board information</h2><p class="text-secondary mb-0">See the published board and official contact information for this club.</p></a></div>
                <div class="col-md-6"><a class="content-panel p-4 p-lg-5 h-100 d-block text-decoration-none text-reset" href="{{ route('clubs.renewal', $club) }}"><div class="section-label mb-2">Renewal</div><h2 class="h3 fw-bold mb-2">Membership renewal</h2><p class="text-secondary mb-0">Review deadlines, fee details, and how to renew membership with the club.</p></a></div>
            </div>
        </div>
    </div>
@endsection
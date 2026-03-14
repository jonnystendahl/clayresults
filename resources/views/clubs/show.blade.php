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
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Members</div>
                        <div class="stats-value">{{ $club->memberships->count() }}</div>
                        <div class="text-secondary">Tracked memberships connected to this club</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Contacts</div>
                        <div class="stats-value">{{ $leadershipMemberships->count() }}</div>
                        <div class="text-secondary">Board members or officials currently published here</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Modules</div>
                        <div class="stats-value">4</div>
                        <div class="text-secondary">News, events, board information, and renewal placeholders</div>
                    </div>
                </div>
            </div>

            <div class="content-panel p-4 p-lg-5 mb-4" id="club-news">
                <div class="section-label mb-2">News</div>
                <h2 class="h3 fw-bold mb-3">Club news placeholder</h2>
                <p class="text-secondary mb-0">Use this section later for announcements, competition results, clubhouse updates, seasonal information, or range notices.</p>
            </div>

            <div class="content-panel p-4 p-lg-5 mb-4" id="club-events">
                <div class="section-label mb-2">Events</div>
                <h2 class="h3 fw-bold mb-3">Upcoming events placeholder</h2>
                <p class="text-secondary mb-0">This area can later show training nights, competitions, board meetings, workdays, or booking information for the range.</p>
            </div>

            <div class="content-panel p-4 p-lg-5 mb-4" id="club-board">
                <div class="section-label mb-2">Board information</div>
                <h2 class="h3 fw-bold mb-3">Board and officials placeholder</h2>
                <p class="text-secondary mb-4">You can later publish the current board, officials, and contact roles here. Only public-facing leadership roles are shown on this page.</p>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Contact</th>
                                <th scope="col">Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($leadershipMemberships as $membership)
                                <tr>
                                    <td class="fw-semibold">{{ $membership->user->name }}</td>
                                    <td>{{ $membership->role }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-secondary">No public board or official roles are published for this club yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="content-panel p-4 p-lg-5" id="membership-renewal">
                <div class="section-label mb-2">Membership renewal</div>
                <h2 class="h3 fw-bold mb-3">Renewal guidance placeholder</h2>
                <p class="text-secondary mb-0">This section can later explain how to join the club, renew membership, pay fees, and see deadlines for annual renewal.</p>
            </div>
        </div>
    </div>
@endsection
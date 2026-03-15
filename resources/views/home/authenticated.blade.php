@extends('layouts.app', ['title' => ($mainClub?->name ? $mainClub->name.' | ClayResults' : 'Your Club | ClayResults')])

@section('content')
    @if ($mainClub === null)
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="content-panel p-4 p-lg-5 text-center">
                    <div class="section-label mb-2">No main club yet</div>
                    <h1 class="h2 fw-bold mb-3">You are not connected to a club yet</h1>
                    <p class="text-secondary mb-0">Once an administrator adds you to a club, your main club page will appear here.</p>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4 g-xl-5 align-items-start">
            <div class="col-xl-4">
                <div class="content-panel p-4 p-lg-5 position-sticky" style="top: 6rem;">
                    <div class="section-label mb-2">Main club</div>
                    <h1 class="h2 fw-bold mb-3">{{ $mainClub->name }}</h1>
                    <p class="text-secondary mb-4">This is your selected club home. Use the club menu in the top navigation if you want to switch to another club you belong to.</p>

                    <div class="vstack gap-3 mb-4">
                        <div class="result-card p-4">
                            <div class="section-label mb-2">Location</div>
                            <div class="fw-semibold">{{ $mainClub->address ?: 'Address not set yet.' }}</div>
                        </div>
                        <div class="result-card p-4">
                            <div class="section-label mb-2">Contact person</div>
                            <div class="fw-semibold">{{ $mainClub->contact_person_name ?: 'No contact person yet' }}</div>
                            @if ($mainClub->contact_person_email)
                                <div class="text-secondary small mt-2">{{ $mainClub->contact_person_email }}</div>
                            @endif
                            @if ($mainClub->contact_person_phone)
                                <div class="text-secondary small">{{ $mainClub->contact_person_phone }}</div>
                            @endif
                        </div>
                    </div>

                    @if ($mainClub->note)
                        <div class="result-card p-4">
                            <div class="section-label mb-2">Club note</div>
                            <div class="text-secondary">{{ $mainClub->note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-8">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="stats-card p-4 h-100">
                            <div class="section-label mb-2">Members</div>
                            <div class="stats-value">{{ $mainClub->memberships->count() }}</div>
                            <div class="text-secondary">Tracked memberships in your main club</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card p-4 h-100">
                            <div class="section-label mb-2">Your role</div>
                            <div class="fs-4 fw-bold">{{ $mainMembership?->role ?? 'Member' }}</div>
                            <div class="text-secondary">{{ $mainMembership?->is_club_admin ? 'You can administer club members and passwords.' : 'Your role in this club right now' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card p-4 h-100">
                            <div class="section-label mb-2">Payment</div>
                            <div class="fs-4 fw-bold">{{ $mainMembership?->is_paid ? 'Paid' : 'Not paid' }}</div>
                            <div class="text-secondary">Based on the latest membership status</div>
                        </div>
                    </div>
                </div>

                <div class="content-panel p-4 p-lg-5 mb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                        <div>
                            <div class="section-label mb-2">Your membership</div>
                            <h2 class="h3 fw-bold mb-0">Membership details for {{ $mainClub->name }}</h2>
                        </div>
                        <a class="btn btn-outline-primary" href="{{ route('training-results.index') }}">Open results</a>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="result-card p-4 h-100">
                                <div class="section-label mb-2">Joined on</div>
                                <div class="fw-semibold">{{ $mainMembership?->joined_on?->format('Y-m-d') ?? 'Not set' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card p-4 h-100">
                                <div class="section-label mb-2">Last paid</div>
                                <div class="fw-semibold">{{ $mainMembership?->last_paid_on?->format('Y-m-d') ?? 'Not registered' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card p-4 h-100">
                                <div class="section-label mb-2">Membership ends</div>
                                <div class="fw-semibold">{{ $mainMembership?->ends_on?->format('Y-m-d') ?? 'No end date' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-panel p-4 p-lg-5 mb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                        <div>
                            <div class="section-label mb-2">Your clubs</div>
                            <h2 class="h3 fw-bold mb-0">Club memberships connected to your account</h2>
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach ($clubs as $club)
                            <div class="col-md-6">
                                <div class="result-card p-4 h-100 {{ $mainClub->is($club) ? 'border border-primary-subtle' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                        <div>
                                            <div class="fw-semibold">{{ $club->name }}</div>
                                            <div class="text-secondary small">{{ $club->address ?: 'Address not set yet.' }}</div>
                                        </div>
                                        @if ($mainClub->is($club))
                                            <span class="badge text-bg-success">Main club</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6"><a class="content-panel p-4 p-lg-5 h-100 d-block text-decoration-none text-reset" href="{{ route('clubs.news', $mainClub) }}"><div class="section-label mb-2">News</div><h2 class="h3 fw-bold mb-2">Club news</h2><p class="text-secondary mb-0">{{ $mainClub->newsPosts->isNotEmpty() ? 'Read the latest published updates from your main club.' : 'No published news yet. This page will show updates when the club starts posting.' }}</p></a></div>
                    <div class="col-md-6"><a class="content-panel p-4 p-lg-5 h-100 d-block text-decoration-none text-reset" href="{{ route('clubs.events', $mainClub) }}"><div class="section-label mb-2">Events</div><h2 class="h3 fw-bold mb-2">Events and training</h2><p class="text-secondary mb-0">{{ $mainClub->events->isNotEmpty() ? 'Open the event page to see upcoming training, competitions, and meetings.' : 'No published events yet. This page will show the club calendar when available.' }}</p></a></div>
                    <div class="col-md-6"><a class="content-panel p-4 p-lg-5 h-100 d-block text-decoration-none text-reset" href="{{ route('clubs.board', $mainClub) }}"><div class="section-label mb-2">Board</div><h2 class="h3 fw-bold mb-2">Board information</h2><p class="text-secondary mb-0">{{ $mainClub->boardMembers->isNotEmpty() ? 'See public board and official contacts for your club.' : 'No public board information has been published yet.' }}</p></a></div>
                    <div class="col-md-6"><a class="content-panel p-4 p-lg-5 h-100 d-block text-decoration-none text-reset" href="{{ route('clubs.renewal', $mainClub) }}"><div class="section-label mb-2">Renewal</div><h2 class="h3 fw-bold mb-2">Membership renewal</h2><p class="text-secondary mb-0">{{ $mainClub->renewalSetting?->is_open ? 'Renewal is open now. Review fees, deadlines, and request status.' : 'Review fees, deadlines, and renewal information for your main club.' }}</p></a></div>
                </div>

                <div class="content-panel p-4 p-lg-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                        <div>
                            <div class="section-label mb-2">Roster</div>
                            <h2 class="h3 fw-bold mb-0">Members in {{ $mainClub->name }}</h2>
                        </div>
                        @if ($canManageMainClub)
                            <div class="text-secondary">Club administrators can set temporary passwords for members in this club.</div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Member</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Ends</th>
                                    @if ($canManageMainClub)
                                        <th class="text-end" scope="col">Temporary password</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mainClub->memberships as $membership)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $membership->user->name }}
                                            @if ($membership->user->is(auth()->user()))
                                                <span class="badge text-bg-light border ms-2">You</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $membership->role }}
                                            @if ($membership->is_club_admin)
                                                <span class="badge text-bg-warning ms-2">Club admin</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($membership->is_paid)
                                                <span class="badge text-bg-success">Paid</span>
                                            @else
                                                <span class="badge text-bg-secondary">Not paid</span>
                                            @endif
                                        </td>
                                        <td>{{ $membership->ends_on?->format('Y-m-d') ?? 'No end date' }}</td>
                                        @if ($canManageMainClub)
                                            <td class="text-end">
                                                <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#temporary-password-{{ $membership->id }}" aria-expanded="false" aria-controls="temporary-password-{{ $membership->id }}">
                                                    Set temporary password
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                    @if ($canManageMainClub)
                                        <tr class="collapse" id="temporary-password-{{ $membership->id }}">
                                            <td colspan="5">
                                                <div class="result-card p-4 my-2">
                                                    <div class="fw-semibold mb-2">Temporary password for {{ $membership->user->name }}</div>
                                                    <p class="text-secondary small mb-3">Set a temporary password that the member can use to log in and change later.</p>

                                                    <form method="POST" action="{{ route('clubs.members.password.store', [$mainClub, $membership->user]) }}" class="row g-3 align-items-end">
                                                        @csrf

                                                        <div class="col-md-5">
                                                            <label class="form-label fw-semibold" for="password-{{ $membership->id }}">Temporary password</label>
                                                            <input class="form-control @error('password') is-invalid @enderror" id="password-{{ $membership->id }}" name="password" type="password" required>
                                                        </div>

                                                        <div class="col-md-5">
                                                            <label class="form-label fw-semibold" for="password_confirmation-{{ $membership->id }}">Confirm password</label>
                                                            <input class="form-control" id="password_confirmation-{{ $membership->id }}" name="password_confirmation" type="password" required>
                                                        </div>

                                                        <div class="col-md-2 d-grid">
                                                            <button class="btn btn-primary" type="submit">Save</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
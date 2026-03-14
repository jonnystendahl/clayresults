@php use Illuminate\Support\Str; @endphp

@extends('layouts.app', ['title' => 'Club Administration | ClayResults'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-4">
            <div class="content-panel p-4 p-lg-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">Administration</div>
                <h1 class="h3 fw-bold mb-3">Manage clubs</h1>
                <p class="text-secondary mb-4">Create clubs, keep contact details current, and track each member's role, paid status, and membership dates.</p>
                <a class="btn btn-primary btn-lg w-100" href="{{ route('admin.clubs.create') }}">Add club</a>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Clubs</div>
                        <div class="stats-value">{{ $stats['clubs'] }}</div>
                        <div class="text-secondary">Registered shooting clubs</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Memberships</div>
                        <div class="stats-value">{{ $stats['memberships'] }}</div>
                        <div class="text-secondary">Tracked club memberships</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Paid</div>
                        <div class="stats-value">{{ $stats['paidMemberships'] }}</div>
                        <div class="text-secondary">Memberships marked as paid</div>
                    </div>
                </div>
            </div>

            <div class="content-panel p-4 p-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Directory</div>
                        <h2 class="h3 fw-bold mb-0">All clubs</h2>
                    </div>
                    <div class="text-secondary">{{ $clubs->count() }} {{ Str::plural('club', $clubs->count()) }}</div>
                </div>

                @if ($clubs->isEmpty())
                    <div class="result-card p-4 p-lg-5 text-center">
                        <h3 class="h4 fw-semibold mb-2">No clubs yet</h3>
                        <p class="text-secondary mb-0">Create the first club to start tracking memberships.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Club</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Contact</th>
                                    <th scope="col">Members</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clubs as $club)
                                    <tr>
                                        <td class="fw-semibold">{{ $club->name }}</td>
                                        <td>{{ $club->address ?: 'No address saved.' }}</td>
                                        <td>
                                            <div>{{ $club->contact_person_name ?: 'No contact person' }}</div>
                                            @if ($club->contact_person_email)
                                                <div class="text-secondary small">{{ $club->contact_person_email }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $club->memberships_count }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.clubs.edit', $club) }}">Manage</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
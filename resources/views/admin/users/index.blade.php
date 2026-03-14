@php use Illuminate\Support\Str; @endphp

@extends('layouts.app', ['title' => 'User Administration | ClayResults'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-4">
            <div class="content-panel p-4 p-lg-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">Administration</div>
                <h1 class="h3 fw-bold mb-3">Manage users</h1>
                <p class="text-secondary mb-0">Review all registered shooters, update profile details, and choose who has administrator access.</p>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Users</div>
                        <div class="stats-value">{{ $stats['total'] }}</div>
                        <div class="text-secondary">Total registered accounts</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Administrators</div>
                        <div class="stats-value">{{ $stats['admins'] }}</div>
                        <div class="text-secondary">Accounts with admin access</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Members</div>
                        <div class="stats-value">{{ $stats['members'] }}</div>
                        <div class="text-secondary">Standard user accounts</div>
                    </div>
                </div>
            </div>

            <div class="content-panel p-4 p-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Directory</div>
                        <h2 class="h3 fw-bold mb-0">All users</h2>
                    </div>
                    <div class="text-secondary">{{ $users->count() }} registered {{ Str::plural('user', $users->count()) }}</div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $user->name }}
                                        @if (auth()->id() === $user->id)
                                            <span class="badge text-bg-light border ms-2">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->is_admin)
                                            <span class="badge text-bg-success">Administrator</span>
                                        @else
                                            <span class="badge text-bg-secondary">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit', $user) }}">Edit user</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
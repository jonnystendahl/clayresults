@extends('layouts.app', ['title' => 'Edit User | ClayResults'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="content-panel p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Administration</div>
                        <h1 class="h2 fw-bold mb-0">Edit user</h1>
                    </div>
                    <a class="btn btn-outline-primary" href="{{ route('admin.users.index') }}">Back to users</a>
                </div>

                <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="name">Name</label>
                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $managedUser->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $managedUser->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="result-card p-4">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input @error('is_admin') is-invalid @enderror" id="is_admin" name="is_admin" type="checkbox" role="switch" value="1" @checked(old('is_admin', $managedUser->is_admin))>
                                <label class="form-check-label fw-semibold" for="is_admin">Administrator access</label>
                                <div class="text-secondary mt-2">Administrators can open the admin area and manage all users.</div>
                                @error('is_admin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex flex-column flex-md-row gap-2 justify-content-between mt-3">
                        <a class="btn btn-outline-primary" href="{{ route('admin.users.index') }}">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save user</button>
                    </div>
                </form>

                <hr class="my-4">

                <div>
                    <div class="section-label mb-2">Club memberships</div>
                    <h2 class="h4 fw-bold mb-3">Current club roles</h2>

                    @if ($managedUser->clubMemberships->isEmpty())
                        <p class="text-secondary mb-0">This user is not assigned to any club yet.</p>
                    @else
                        <div class="vstack gap-3">
                            @foreach ($managedUser->clubMemberships->sortBy(fn ($membership) => $membership->club->name) as $membership)
                                <div class="result-card p-3 p-md-4">
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                        <div>
                                            <div class="fw-semibold">{{ $membership->club->name }}</div>
                                            <div class="text-secondary">{{ $membership->role }}</div>
                                        </div>
                                        <div class="text-secondary text-md-end">
                                            <div>Joined {{ $membership->joined_on->format('Y-m-d') }}</div>
                                            <div>{{ $membership->is_paid ? 'Membership paid' : 'Membership not paid' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
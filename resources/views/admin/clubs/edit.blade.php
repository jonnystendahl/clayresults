@extends('layouts.app', ['title' => 'Edit Club | ClayResults'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-5">
            <div class="content-panel p-4 p-md-5 position-sticky" style="top: 6rem;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Administration</div>
                        <h1 class="h2 fw-bold mb-0">Edit club</h1>
                    </div>
                    <a class="btn btn-outline-primary" href="{{ route('admin.clubs.index') }}">Back to clubs</a>
                </div>

                <form method="POST" action="{{ route('admin.clubs.update', $club) }}" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="name">Club name</label>
                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $club->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="address">Address</label>
                        <input class="form-control @error('address') is-invalid @enderror" id="address" name="address" type="text" value="{{ old('address', $club->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="contact_person_name">Contact person</label>
                        <input class="form-control @error('contact_person_name') is-invalid @enderror" id="contact_person_name" name="contact_person_name" type="text" value="{{ old('contact_person_name', $club->contact_person_name) }}">
                        @error('contact_person_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="contact_person_email">Contact email</label>
                        <input class="form-control @error('contact_person_email') is-invalid @enderror" id="contact_person_email" name="contact_person_email" type="email" value="{{ old('contact_person_email', $club->contact_person_email) }}">
                        @error('contact_person_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="contact_person_phone">Contact phone</label>
                        <input class="form-control @error('contact_person_phone') is-invalid @enderror" id="contact_person_phone" name="contact_person_phone" type="text" value="{{ old('contact_person_phone', $club->contact_person_phone) }}">
                        @error('contact_person_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="note">Club note</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="4">{{ old('note', $club->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-flex justify-content-end mt-3">
                        <button class="btn btn-primary" type="submit">Save club</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.clubs.destroy', $club) }}" class="mt-3 pt-3 border-top">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-secondary" type="submit" onclick="return confirm('Delete this club and all memberships?');">Delete club</button>
                </form>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="content-panel p-4 p-lg-5 mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Club modules</div>
                        <h2 class="h3 fw-bold mb-0">Manage public and member pages</h2>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6"><a class="result-card p-4 h-100 d-block text-decoration-none text-reset" href="{{ route('admin.clubs.news.index', $club) }}"><div class="fw-semibold mb-2">News posts</div><div class="text-secondary">Publish club news shown on the public club site.</div></a></div>
                    <div class="col-md-6"><a class="result-card p-4 h-100 d-block text-decoration-none text-reset" href="{{ route('admin.clubs.events.index', $club) }}"><div class="fw-semibold mb-2">Events</div><div class="text-secondary">Manage competitions, training nights, and meetings.</div></a></div>
                    <div class="col-md-6"><a class="result-card p-4 h-100 d-block text-decoration-none text-reset" href="{{ route('admin.clubs.board.index', $club) }}"><div class="fw-semibold mb-2">Board information</div><div class="text-secondary">Publish public board and official contact entries.</div></a></div>
                    <div class="col-md-6"><a class="result-card p-4 h-100 d-block text-decoration-none text-reset" href="{{ route('admin.clubs.renewal.edit', $club) }}"><div class="fw-semibold mb-2">Membership renewal</div><div class="text-secondary">Configure renewal deadlines, fees, and review requests.</div></a></div>
                </div>
            </div>

            <div class="content-panel p-4 p-lg-5 mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Memberships</div>
                        <h2 class="h3 fw-bold mb-0">Add member to {{ $club->name }}</h2>
                    </div>
                    <div class="text-secondary">{{ $memberships->count() }} tracked memberships</div>
                </div>

                <form method="POST" action="{{ route('admin.clubs.memberships.store', $club) }}" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="user_id">User</label>
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                            <option value="">Choose user</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="role">Role</label>
                        <input class="form-control @error('role') is-invalid @enderror" id="role" name="role" type="text" value="{{ old('role') }}" placeholder="Member" required>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="joined_on">Joined on</label>
                        <input class="form-control @error('joined_on') is-invalid @enderror" id="joined_on" name="joined_on" type="date" value="{{ old('joined_on', now()->toDateString()) }}" required>
                        @error('joined_on')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="last_paid_on">Last paid on</label>
                        <input class="form-control @error('last_paid_on') is-invalid @enderror" id="last_paid_on" name="last_paid_on" type="date" value="{{ old('last_paid_on') }}">
                        @error('last_paid_on')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="ends_on">Membership ends</label>
                        <input class="form-control @error('ends_on') is-invalid @enderror" id="ends_on" name="ends_on" type="date" value="{{ old('ends_on') }}">
                        @error('ends_on')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="result-card p-4">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input @error('is_paid') is-invalid @enderror" id="is_paid" name="is_paid" type="checkbox" role="switch" value="1" @checked(old('is_paid'))>
                                <label class="form-check-label fw-semibold" for="is_paid">Membership paid</label>
                                <div class="text-secondary mt-2">Turn this on when the current membership fee has been paid.</div>
                                @error('is_paid')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">Add membership</button>
                    </div>
                </form>
            </div>

            <div class="content-panel p-4 p-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Roster</div>
                        <h2 class="h3 fw-bold mb-0">Club members</h2>
                    </div>
                </div>

                @if ($memberships->isEmpty())
                    <div class="result-card p-4 p-lg-5 text-center">
                        <h3 class="h4 fw-semibold mb-2">No memberships yet</h3>
                        <p class="text-secondary mb-0">Add the first club member using the form above.</p>
                    </div>
                @else
                    <div class="vstack gap-3">
                        @foreach ($memberships as $membership)
                            <div class="result-card p-4">
                                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                                    <div>
                                        <div class="fw-semibold">{{ $membership->user->name }}</div>
                                        <div class="text-secondary small">{{ $membership->user->email }}</div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @if ($membership->is_paid)
                                            <span class="badge text-bg-success">Paid</span>
                                        @else
                                            <span class="badge text-bg-secondary">Not paid</span>
                                        @endif
                                        @if ($membership->ends_on && $membership->ends_on->isPast())
                                            <span class="badge text-bg-warning">Expired</span>
                                        @endif
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.clubs.memberships.update', [$club, $membership]) }}" class="row g-3 align-items-end">
                                    @csrf
                                    @method('PUT')

                                    <input name="user_id" type="hidden" value="{{ $membership->user_id }}">

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold" for="role-{{ $membership->id }}">Role</label>
                                        <input class="form-control" id="role-{{ $membership->id }}" name="role" type="text" value="{{ old('role', $membership->role) }}" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold" for="joined_on-{{ $membership->id }}">Joined on</label>
                                        <input class="form-control" id="joined_on-{{ $membership->id }}" name="joined_on" type="date" value="{{ old('joined_on', $membership->joined_on?->toDateString()) }}" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold" for="last_paid_on-{{ $membership->id }}">Last paid</label>
                                        <input class="form-control" id="last_paid_on-{{ $membership->id }}" name="last_paid_on" type="date" value="{{ old('last_paid_on', $membership->last_paid_on?->toDateString()) }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold" for="ends_on-{{ $membership->id }}">Ends</label>
                                        <input class="form-control" id="ends_on-{{ $membership->id }}" name="ends_on" type="date" value="{{ old('ends_on', $membership->ends_on?->toDateString()) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" id="is_paid-{{ $membership->id }}" name="is_paid" type="checkbox" role="switch" value="1" @checked(old('is_paid', $membership->is_paid))>
                                            <label class="form-check-label fw-semibold" for="is_paid-{{ $membership->id }}">Membership paid</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 d-flex justify-content-md-end">
                                        <button class="btn btn-primary" type="submit">Save membership</button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('admin.clubs.memberships.destroy', [$club, $membership]) }}" class="mt-3 d-flex justify-content-end">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-secondary" type="submit" onclick="return confirm('Remove this membership?');">Remove</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
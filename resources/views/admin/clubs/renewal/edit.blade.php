@extends('layouts.app', ['title' => 'Club Renewal | ClayResults'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-5">
            <div class="content-panel p-4 p-lg-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">Administration</div>
                <h1 class="h2 fw-bold mb-3">Membership renewal</h1>
                <p class="text-secondary mb-4">Configure the public/member-facing renewal page for {{ $club->name }}.</p>
                <form method="POST" action="{{ route('admin.clubs.renewal.update', $club) }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-12"><label class="form-label fw-semibold" for="season_label">Season label</label><input class="form-control" id="season_label" name="season_label" type="text" value="{{ old('season_label', $renewalSetting->season_label) }}" placeholder="2026"></div>
                    <div class="col-12"><label class="form-label fw-semibold" for="renewal_title">Title</label><input class="form-control" id="renewal_title" name="title" type="text" value="{{ old('title', $renewalSetting->title) }}" placeholder="Renew your membership"></div>
                    <div class="col-12"><label class="form-label fw-semibold" for="renewal_description">Description</label><textarea class="form-control" id="renewal_description" name="description" rows="4">{{ old('description', $renewalSetting->description) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="fee_amount">Fee amount</label><input class="form-control" id="fee_amount" name="fee_amount" type="number" min="0" step="0.01" value="{{ old('fee_amount', $renewalSetting->fee_amount) }}"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="fee_currency">Currency</label><input class="form-control" id="fee_currency" name="fee_currency" type="text" value="{{ old('fee_currency', $renewalSetting->fee_currency ?: 'SEK') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="renewal_deadline">Deadline</label><input class="form-control" id="renewal_deadline" name="renewal_deadline" type="date" value="{{ old('renewal_deadline', $renewalSetting->renewal_deadline?->toDateString()) }}"></div>
                    <div class="col-12"><label class="form-label fw-semibold" for="payment_details">Payment details</label><textarea class="form-control" id="payment_details" name="payment_details" rows="4">{{ old('payment_details', $renewalSetting->payment_details) }}</textarea></div>
                    <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" id="renewal_is_open" name="is_open" type="checkbox" value="1" @checked(old('is_open', $renewalSetting->is_open))><label class="form-check-label" for="renewal_is_open">Renewal requests are open</label></div></div>
                    <div class="col-12 d-flex justify-content-between"><a class="btn btn-outline-primary" href="{{ route('admin.clubs.edit', $club) }}">Back to club</a><button class="btn btn-primary" type="submit">Save renewal settings</button></div>
                </form>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="content-panel p-4 p-lg-5">
                <div class="section-label mb-2">Requests</div>
                <h2 class="h3 fw-bold mb-4">Renewal requests</h2>
                <div class="vstack gap-3">
                    @forelse ($renewalRequests as $renewalRequest)
                        <div class="result-card p-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                                <div>
                                    <div class="fw-semibold">{{ $renewalRequest->membership->user->name }}</div>
                                    <div class="text-secondary small">{{ $renewalRequest->season_label ?: 'Current season' }}</div>
                                </div>
                                <span class="badge text-bg-light border">{{ ucfirst($renewalRequest->status) }}</span>
                            </div>
                            <div class="text-secondary mb-3">{{ $renewalRequest->note ?: 'No note provided.' }}</div>
                            <form method="POST" action="{{ route('admin.clubs.renewal.requests.update', [$club, $renewalRequest]) }}" class="row g-3 align-items-end">
                                @csrf
                                @method('PUT')
                                <div class="col-md-4"><label class="form-label fw-semibold" for="status-{{ $renewalRequest->id }}">Status</label><select class="form-select" id="status-{{ $renewalRequest->id }}" name="status"><option value="pending" @selected($renewalRequest->status === 'pending')>Pending</option><option value="approved" @selected($renewalRequest->status === 'approved')>Approved</option><option value="rejected" @selected($renewalRequest->status === 'rejected')>Rejected</option></select></div>
                                <div class="col-md-8"><label class="form-label fw-semibold" for="admin_note-{{ $renewalRequest->id }}">Admin note</label><textarea class="form-control" id="admin_note-{{ $renewalRequest->id }}" name="admin_note" rows="2">{{ $renewalRequest->admin_note }}</textarea></div>
                                <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit">Update request</button></div>
                            </form>
                        </div>
                    @empty
                        <div class="result-card p-4 p-lg-5 text-center"><p class="text-secondary mb-0">No renewal requests yet.</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
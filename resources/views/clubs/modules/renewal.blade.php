@extends('layouts.app', ['title' => $club->name.' Renewal | ClayResults'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-5">
            <div class="content-panel p-4 p-lg-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">Membership renewal</div>
                <h1 class="h2 fw-bold mb-3">{{ $renewalSetting?->title ?: 'Renew membership in '.$club->name }}</h1>
                <p class="text-secondary mb-4">{{ $renewalSetting?->description ?: 'Use this page to review membership renewal information and submit your renewal request.' }}</p>
                <div class="vstack gap-3">
                    <div class="result-card p-4"><div class="section-label mb-2">Season</div><div class="fw-semibold">{{ $renewalSetting?->season_label ?: now()->format('Y') }}</div></div>
                    <div class="result-card p-4"><div class="section-label mb-2">Deadline</div><div class="fw-semibold">{{ $renewalSetting?->renewal_deadline?->format('Y-m-d') ?: 'No deadline published yet' }}</div></div>
                    <div class="result-card p-4"><div class="section-label mb-2">Fee</div><div class="fw-semibold">{{ $renewalSetting?->fee_amount ? number_format((float) $renewalSetting->fee_amount, 2).' '.$renewalSetting->fee_currency : 'Fee not published yet' }}</div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="content-panel p-4 p-lg-5 mb-4">
                <div class="section-label mb-2">Payment details</div>
                <h2 class="h3 fw-bold mb-3">How renewal works</h2>
                <p class="text-secondary mb-0" style="white-space: pre-line;">{{ $renewalSetting?->payment_details ?: 'Payment instructions have not been published yet.' }}</p>
            </div>

            @auth
                @if ($membership !== null)
                    <div class="content-panel p-4 p-lg-5 mb-4">
                        <div class="section-label mb-2">Your status</div>
                        <h2 class="h3 fw-bold mb-3">Membership renewal status</h2>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4"><div class="result-card p-4 h-100"><div class="section-label mb-2">Current fee status</div><div class="fw-semibold">{{ $membership->is_paid ? 'Paid' : 'Not paid' }}</div></div></div>
                            <div class="col-md-4"><div class="result-card p-4 h-100"><div class="section-label mb-2">Membership ends</div><div class="fw-semibold">{{ $membership->ends_on?->format('Y-m-d') ?: 'No end date' }}</div></div></div>
                            <div class="col-md-4"><div class="result-card p-4 h-100"><div class="section-label mb-2">Latest request</div><div class="fw-semibold">{{ $latestRenewalRequest ? ucfirst($latestRenewalRequest->status) : 'None' }}</div></div></div>
                        </div>
                        @if ($latestRenewalRequest)
                            <div class="result-card p-4 mb-4">
                                <div class="section-label mb-2">Latest request note</div>
                                <div class="text-secondary mb-2">{{ $latestRenewalRequest->note ?: 'No note provided.' }}</div>
                                @if ($latestRenewalRequest->admin_note)
                                    <div class="small text-secondary">Admin note: {{ $latestRenewalRequest->admin_note }}</div>
                                @endif
                            </div>
                        @endif

                        @if ($renewalSetting?->is_open)
                            <form method="POST" action="{{ route('clubs.renewal.store', $club) }}" class="row g-3">
                                @csrf
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="renewal_note">Request note</label>
                                    <textarea class="form-control @error('note') is-invalid @enderror" id="renewal_note" name="note" rows="4">{{ old('note') }}</textarea>
                                    @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit">Submit renewal request</button></div>
                            </form>
                        @else
                            <div class="result-card p-4"><p class="text-secondary mb-0">Renewal requests are currently closed for this club.</p></div>
                        @endif
                    </div>
                @else
                    <div class="content-panel p-4 p-lg-5"><p class="text-secondary mb-0">You must be a member of this club to submit a renewal request.</p></div>
                @endif
            @else
                <div class="content-panel p-4 p-lg-5"><p class="text-secondary mb-3">Log in to view your membership status and submit a renewal request.</p><a class="btn btn-primary" href="{{ route('login') }}">Log in</a></div>
            @endauth
        </div>
    </div>
@endsection
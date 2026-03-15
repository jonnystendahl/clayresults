@extends('layouts.app', ['title' => 'Verify Email | KlubbManager'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="auth-panel p-4 p-md-5">
                <div class="section-label mb-2">Verify account</div>
                <h1 class="h2 fw-bold mb-3">Confirm your email address</h1>
                <p class="text-secondary mb-4">We sent a verification link to your email address. Open the email and click the link within 1 hour before using the rest of the app.</p>

                <div class="rounded-4 border p-3 p-md-4 bg-white bg-opacity-50 mb-4">
                    <p class="mb-2 fw-semibold">Until your email is verified, your account is limited.</p>
                    <p class="text-secondary mb-0">You can request a new verification email from this page or log out and come back later.</p>
                </div>

                <form method="POST" action="{{ route('verification.send') }}" class="d-grid gap-3">
                    @csrf
                    <button class="btn btn-primary btn-lg" type="submit">Send new verification email</button>
                </form>

                <p class="text-secondary mb-0 mt-4">Check your spam folder if the message does not arrive right away.</p>
            </div>
        </div>
    </div>
@endsection
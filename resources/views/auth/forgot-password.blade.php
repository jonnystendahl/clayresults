@extends('layouts.app', ['title' => 'Forgot Password | KlubbManager'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <div class="auth-panel p-4 p-md-5">
                <div class="section-label mb-2">Password reset</div>
                <h1 class="h2 fw-bold mb-3">Request a password reset link</h1>
                <p class="text-secondary mb-4">Enter your email address and we will send you a secure link to choose a new password.</p>

                <form method="POST" action="{{ route('password.email') }}" class="vstack gap-3">
                    @csrf

                    <div>
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-primary btn-lg mt-2" type="submit">Send reset link</button>
                </form>

                <p class="text-secondary mb-0 mt-4">
                    Remembered it?
                    <a class="link-primary fw-semibold text-decoration-none" href="{{ route('login') }}">Go back to log in</a>
                </p>
            </div>
        </div>
    </div>
@endsection
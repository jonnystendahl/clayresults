@extends('layouts.app', ['title' => 'Register | KlubbManager'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="auth-panel p-4 p-md-5">
                <div class="section-label mb-2">Create account</div>
                <h1 class="h2 fw-bold mb-3">Start your clay shooting result log</h1>
                <p class="text-secondary mb-4">Create a personal account and keep your training history separate from every other shooter.</p>

                <form method="POST" action="{{ route('register') }}" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="name">Name</label>
                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="password">Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="password_confirmation">Confirm password</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
                    </div>

                    <div class="col-12 d-grid mt-2">
                        <button class="btn btn-primary btn-lg" type="submit">Create account</button>
                    </div>
                </form>

                <p class="text-secondary mb-0 mt-4">
                    Already registered?
                    <a class="link-primary fw-semibold text-decoration-none" href="{{ route('login') }}">Log in</a>
                </p>
            </div>
        </div>
    </div>
@endsection
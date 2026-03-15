@extends('layouts.app', ['title' => $loginTitle ?? 'Log in | KlubbManager'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <div class="auth-panel p-4 p-md-5">
                <div class="section-label mb-2">{{ $loginEyebrow ?? 'Welcome back' }}</div>
                <h1 class="h2 fw-bold mb-3">{{ $loginHeading ?? 'Log in to your club account' }}</h1>
                <p class="text-secondary mb-4">{{ $loginDescription ?? 'Pick up where you left off and register the next round for your current club.' }}</p>

                <form method="POST" action="{{ $loginAction ?? route('login') }}" class="vstack gap-3">
                    @csrf

                    <div>
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="password">Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a class="link-primary fw-semibold text-decoration-none" href="{{ route('password.request') }}">Forgot your password?</a>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                        <label class="form-check-label" for="remember">Keep me signed in</label>
                    </div>

                    <button class="btn btn-primary btn-lg mt-2" type="submit">{{ $loginButtonLabel ?? 'Log in' }}</button>
                </form>

                @if (($showRegisterLink ?? true) === true)
                    <p class="text-secondary mb-0 mt-4">
                        Need an account?
                        <a class="link-primary fw-semibold text-decoration-none" href="{{ route('register') }}">Register here</a>
                    </p>
                @endif

                @if (! empty($alternateLoginUrl) && ! empty($alternateLoginLabel))
                    <p class="text-secondary mb-0 mt-3">
                        <a class="link-primary fw-semibold text-decoration-none" href="{{ $alternateLoginUrl }}">{{ $alternateLoginLabel }}</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app', ['title' => 'Reset Password | KlubbManager'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <div class="auth-panel p-4 p-md-5">
                <div class="section-label mb-2">Choose new password</div>
                <h1 class="h2 fw-bold mb-3">Set your new password</h1>
                <p class="text-secondary mb-4">Use a strong password you have not used before on this account.</p>

                <form method="POST" action="{{ route('password.store') }}" class="vstack gap-3">
                    @csrf

                    <input name="token" type="hidden" value="{{ $token }}">

                    <div>
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="password">New password</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="password_confirmation">Confirm new password</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
                    </div>

                    <button class="btn btn-primary btn-lg mt-2" type="submit">Reset password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
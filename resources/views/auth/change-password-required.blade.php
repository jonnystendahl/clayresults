@extends('layouts.app', ['title' => 'Change Password | KlubbManager'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="auth-panel p-4 p-md-5">
                <div class="section-label mb-2">Password update required</div>
                <h1 class="h2 fw-bold mb-3">Choose a personal password</h1>
                <p class="text-secondary mb-4">You signed in with a temporary password. Before you can use the rest of the app, choose a new password that only you know.</p>

                <form method="POST" action="{{ route('password.change.update') }}" class="vstack gap-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="form-label fw-semibold" for="current_password">Temporary password</label>
                        <input class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" type="password" required autofocus>
                        @error('current_password')
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

                    <button class="btn btn-primary btn-lg mt-2" type="submit">Save new password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
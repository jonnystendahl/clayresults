@extends('layouts.app', ['title' => 'Add Club | ClayResults'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="content-panel p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Administration</div>
                        <h1 class="h2 fw-bold mb-0">Add club</h1>
                    </div>
                    <a class="btn btn-outline-primary" href="{{ route('admin.clubs.index') }}">Back to clubs</a>
                </div>

                <form method="POST" action="{{ route('admin.clubs.store') }}" class="row g-3">
                    @csrf

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="name">Club name</label>
                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="address">Address</label>
                        <input class="form-control @error('address') is-invalid @enderror" id="address" name="address" type="text" value="{{ old('address') }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="contact_person_name">Contact person</label>
                        <input class="form-control @error('contact_person_name') is-invalid @enderror" id="contact_person_name" name="contact_person_name" type="text" value="{{ old('contact_person_name') }}">
                        @error('contact_person_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="contact_person_email">Contact email</label>
                        <input class="form-control @error('contact_person_email') is-invalid @enderror" id="contact_person_email" name="contact_person_email" type="email" value="{{ old('contact_person_email') }}">
                        @error('contact_person_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="contact_person_phone">Contact phone</label>
                        <input class="form-control @error('contact_person_phone') is-invalid @enderror" id="contact_person_phone" name="contact_person_phone" type="text" value="{{ old('contact_person_phone') }}">
                        @error('contact_person_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="note">Club note</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="4">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-flex flex-column flex-md-row gap-2 justify-content-between mt-3">
                        <a class="btn btn-outline-primary" href="{{ route('admin.clubs.index') }}">Cancel</a>
                        <button class="btn btn-primary" type="submit">Create club</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
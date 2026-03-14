@extends('layouts.app', ['title' => 'Edit Result | Clay Results'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="content-panel p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">Update session</div>
                        <h1 class="h2 fw-bold mb-0">Edit training result</h1>
                    </div>
                    <a class="btn btn-outline-primary" href="{{ route('training-results.index') }}">Back to results</a>
                </div>

                <form method="POST" action="{{ route('training-results.update', $trainingResult) }}" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="performed_on">Date</label>
                        <input class="form-control @error('performed_on') is-invalid @enderror" id="performed_on" name="performed_on" type="date" value="{{ old('performed_on', $trainingResult->performed_on->toDateString()) }}" required>
                        @error('performed_on')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="discipline">Discipline</label>
                        <select class="form-select @error('discipline') is-invalid @enderror" id="discipline" name="discipline" required>
                            @foreach ($disciplines as $discipline)
                                <option value="{{ $discipline }}" @selected(old('discipline', $trainingResult->discipline) === $discipline)>{{ $discipline }}</option>
                            @endforeach
                        </select>
                        @error('discipline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold" for="score">Score</label>
                        <input class="form-control @error('score') is-invalid @enderror" id="score" name="score" type="number" min="0" max="999" value="{{ old('score', $trainingResult->score) }}" required>
                        @error('score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="note">Note</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="5">{{ old('note', $trainingResult->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-flex flex-column flex-md-row gap-2 justify-content-between mt-3">
                        <a class="btn btn-outline-primary" href="{{ route('training-results.index') }}">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
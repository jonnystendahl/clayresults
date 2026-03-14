@php use Illuminate\Support\Str; @endphp

@extends('layouts.app', ['title' => 'Your Results | Clay Results'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-4">
            <div class="content-panel p-4 p-lg-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">New entry</div>
                <h1 class="h3 fw-bold mb-3">Register a training result</h1>
                <p class="text-secondary mb-4">Save every session with date, discipline, score, and a note about how the round felt.</p>

                <form method="POST" action="{{ route('training-results.store') }}" class="vstack gap-3">
                    @csrf

                    <div>
                        <label class="form-label fw-semibold" for="performed_on">Date</label>
                        <input class="form-control @error('performed_on') is-invalid @enderror" id="performed_on" name="performed_on" type="date" value="{{ old('performed_on', now()->toDateString()) }}" required>
                        @error('performed_on')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="discipline">Discipline</label>
                        <select class="form-select @error('discipline') is-invalid @enderror" id="discipline" name="discipline" required>
                            <option value="">Choose discipline</option>
                            @foreach ($disciplines as $discipline)
                                <option value="{{ $discipline }}" @selected(old('discipline') === $discipline)>{{ $discipline }}</option>
                            @endforeach
                        </select>
                        @error('discipline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="score">Score</label>
                        <input class="form-control @error('score') is-invalid @enderror" id="score" name="score" type="number" min="0" max="999" value="{{ old('score') }}" placeholder="24" required>
                        @error('score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label fw-semibold" for="note">Note</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="4" placeholder="Weather, tempo, misses, what improved...">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-primary btn-lg mt-2" type="submit">Save result</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Sessions</div>
                        <div class="stats-value">{{ $stats['sessions'] }}</div>
                        <div class="text-secondary">Total recorded practice rounds</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Best score</div>
                        <div class="stats-value">{{ $stats['bestScore'] }}</div>
                        <div class="text-secondary">Highest saved result so far</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card p-4 h-100">
                        <div class="section-label mb-2">Average</div>
                        <div class="stats-value">{{ $stats['averageScore'] }}</div>
                        <div class="text-secondary">Average score across all sessions</div>
                    </div>
                </div>
            </div>

            <div class="content-panel p-4 p-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <div class="section-label mb-2">History</div>
                        <h2 class="h3 fw-bold mb-0">Your latest training results</h2>
                    </div>
                    <div class="text-secondary">{{ $trainingResults->count() }} saved {{ Str::plural('entry', $trainingResults->count()) }}</div>
                </div>

                @if ($trainingResults->isEmpty())
                    <div class="result-card p-4 p-lg-5 text-center">
                        <h3 class="h4 fw-semibold mb-2">No results yet</h3>
                        <p class="text-secondary mb-0">Your first saved round will show up here.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Discipline</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Note</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trainingResults as $trainingResult)
                                    <tr>
                                        <td class="fw-semibold">{{ $trainingResult->performed_on->format('d M Y') }}</td>
                                        <td>{{ $trainingResult->discipline }}</td>
                                        <td><span class="result-score">{{ $trainingResult->score }}</span></td>
                                        <td>
                                            <div class="note-preview">
                                                {{ $trainingResult->note ?: 'No note saved.' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a class="btn btn-sm btn-outline-primary" href="{{ route('training-results.edit', $trainingResult) }}">Edit</a>
                                                <form method="POST" action="{{ route('training-results.destroy', $trainingResult) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-secondary" type="submit" onclick="return confirm('Delete this result?');">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app', ['title' => $club->name.' Events | ClayResults'])

@section('content')
    <div class="content-panel p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div><div class="section-label mb-2">Club events</div><h1 class="h2 fw-bold mb-0">{{ $club->name }}</h1></div>
            <a class="btn btn-outline-primary" href="{{ route('clubs.show', $club) }}">Back to club</a>
        </div>
    </div>
    <div class="vstack gap-3">
        @forelse ($events as $event)
            <div class="content-panel p-4 p-lg-5">
                <div class="section-label mb-2">{{ $event->starts_at->format('Y-m-d H:i') }}</div>
                <h2 class="h3 fw-bold mb-2">{{ $event->title }}</h2>
                <div class="text-secondary mb-3">{{ $event->location ?: 'Location not specified.' }}</div>
                <div class="text-secondary" style="white-space: pre-line;">{{ $event->description }}</div>
            </div>
        @empty
            <div class="content-panel p-4 p-lg-5 text-center"><p class="text-secondary mb-0">No published events yet.</p></div>
        @endforelse
    </div>
@endsection
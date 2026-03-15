@extends('layouts.app', ['title' => $club->name.' News | KlubbManager'])

@section('content')
    <div class="content-panel p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div><div class="section-label mb-2">Club news</div><h1 class="h2 fw-bold mb-0">{{ $club->name }}</h1></div>
            <a class="btn btn-outline-primary" href="{{ route('clubs.show', $club) }}">Back to club</a>
        </div>
    </div>
    <div class="vstack gap-3">
        @forelse ($newsPosts as $newsPost)
            <article class="content-panel p-4 p-lg-5">
                <div class="section-label mb-2">{{ $newsPost->published_at?->format('Y-m-d') ?? 'Draft' }}</div>
                <h2 class="h3 fw-bold mb-3">{{ $newsPost->title }}</h2>
                @if ($newsPost->excerpt)<p class="lead text-secondary mb-3">{{ $newsPost->excerpt }}</p>@endif
                <div class="text-secondary" style="white-space: pre-line;">{{ $newsPost->body }}</div>
            </article>
        @empty
            <div class="content-panel p-4 p-lg-5 text-center"><p class="text-secondary mb-0">No published news yet.</p></div>
        @endforelse
    </div>
@endsection
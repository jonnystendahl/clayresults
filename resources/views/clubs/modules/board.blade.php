@extends('layouts.app', ['title' => $club->name.' Board | ClayResults'])

@section('content')
    <div class="content-panel p-4 p-lg-5 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div><div class="section-label mb-2">Board information</div><h1 class="h2 fw-bold mb-0">{{ $club->name }}</h1></div>
            <a class="btn btn-outline-primary" href="{{ route('clubs.show', $club) }}">Back to club</a>
        </div>
    </div>
    <div class="row g-3">
        @forelse ($boardMembers as $boardMember)
            <div class="col-md-6">
                <div class="content-panel p-4 p-lg-5 h-100">
                    <div class="section-label mb-2">{{ $boardMember->title }}</div>
                    <h2 class="h4 fw-bold mb-2">{{ $boardMember->name }}</h2>
                    @if ($boardMember->email)<div class="text-secondary small">{{ $boardMember->email }}</div>@endif
                    @if ($boardMember->phone)<div class="text-secondary small mb-3">{{ $boardMember->phone }}</div>@endif
                    @if ($boardMember->bio)<div class="text-secondary">{{ $boardMember->bio }}</div>@endif
                </div>
            </div>
        @empty
            <div class="col-12"><div class="content-panel p-4 p-lg-5 text-center"><p class="text-secondary mb-0">No public board information has been published yet.</p></div></div>
        @endforelse
    </div>
@endsection
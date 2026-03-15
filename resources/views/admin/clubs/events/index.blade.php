@extends('layouts.app', ['title' => 'Club Events | KlubbManager'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-4">
            <div class="content-panel p-4 p-md-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">Administration</div>
                <h1 class="h2 fw-bold mb-3">Manage events</h1>
                <p class="text-secondary mb-4">Publish club events, training nights, and meetings for {{ $club->name }}.</p>
                <a class="btn btn-outline-primary w-100" href="{{ route('admin.clubs.edit', $club) }}">Back to club</a>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="content-panel p-4 p-lg-5 mb-4">
                <div class="section-label mb-2">New event</div>
                <h2 class="h3 fw-bold mb-4">Create event</h2>
                <form method="POST" action="{{ route('admin.clubs.events.store', $club) }}" class="row g-3">
                    @csrf
                    <div class="col-md-6"><label class="form-label fw-semibold" for="event_title">Title</label><input class="form-control" id="event_title" name="title" type="text" value="{{ old('title') }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="event_location">Location</label><input class="form-control" id="event_location" name="location" type="text" value="{{ old('location') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="starts_at">Starts at</label><input class="form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="ends_at">Ends at</label><input class="form-control" id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}"></div>
                    <div class="col-12"><label class="form-label fw-semibold" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description') }}</textarea></div>
                    <div class="col-md-6"><label class="form-label fw-semibold" for="event_published_at">Publish at</label><input class="form-control" id="event_published_at" name="published_at" type="datetime-local" value="{{ old('published_at') }}"></div>
                    <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit">Save event</button></div>
                </form>
            </div>
            <div class="vstack gap-3">
                @forelse ($events as $event)
                    <div class="content-panel p-4 p-lg-5">
                        <form method="POST" action="{{ route('admin.clubs.events.update', [$club, $event]) }}" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6"><input class="form-control" name="title" type="text" value="{{ old('title', $event->title) }}" required></div>
                            <div class="col-md-6"><input class="form-control" name="location" type="text" value="{{ old('location', $event->location) }}"></div>
                            <div class="col-md-6"><input class="form-control" name="starts_at" type="datetime-local" value="{{ old('starts_at', $event->starts_at->format('Y-m-d\TH:i')) }}" required></div>
                            <div class="col-md-6"><input class="form-control" name="ends_at" type="datetime-local" value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}"></div>
                            <div class="col-12"><textarea class="form-control" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea></div>
                            <div class="col-md-6"><input class="form-control" name="published_at" type="datetime-local" value="{{ old('published_at', $event->published_at?->format('Y-m-d\TH:i')) }}"></div>
                            <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit">Update event</button></div>
                        </form>
                        <form method="POST" action="{{ route('admin.clubs.events.destroy', [$club, $event]) }}" class="mt-3">@csrf @method('DELETE')<button class="btn btn-outline-secondary" type="submit">Delete</button></form>
                    </div>
                @empty
                    <div class="content-panel p-4 p-lg-5 text-center"><p class="text-secondary mb-0">No events yet.</p></div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
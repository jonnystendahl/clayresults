@extends('layouts.app', ['title' => 'Club News | KlubbManager'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-4">
            <div class="content-panel p-4 p-md-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">Administration</div>
                <h1 class="h2 fw-bold mb-3">Manage club news</h1>
                <p class="text-secondary mb-4">Publish public news posts for {{ $club->name }}.</p>
                <a class="btn btn-outline-primary w-100" href="{{ route('admin.clubs.edit', $club) }}">Back to club</a>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="content-panel p-4 p-lg-5 mb-4">
                <div class="section-label mb-2">New post</div>
                <h2 class="h3 fw-bold mb-4">Create news post</h2>

                <form method="POST" action="{{ route('admin.clubs.news.store', $club) }}" class="row g-3">
                    @csrf
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="title">Title</label>
                        <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" type="text" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="excerpt">Excerpt</label>
                        <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                        @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="body">Body</label>
                        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="6" required>{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="published_at">Publish at</label>
                        <input class="form-control @error('published_at') is-invalid @enderror" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at') }}">
                        @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">Save news post</button>
                    </div>
                </form>
            </div>

            <div class="vstack gap-3">
                @forelse ($newsPosts as $newsPost)
                    <div class="content-panel p-4 p-lg-5">
                        <form method="POST" action="{{ route('admin.clubs.news.update', [$club, $newsPost]) }}" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-12"><input class="form-control" name="title" type="text" value="{{ old('title', $newsPost->title) }}" required></div>
                            <div class="col-12"><textarea class="form-control" name="excerpt" rows="3">{{ old('excerpt', $newsPost->excerpt) }}</textarea></div>
                            <div class="col-12"><textarea class="form-control" name="body" rows="6" required>{{ old('body', $newsPost->body) }}</textarea></div>
                            <div class="col-md-6"><input class="form-control" name="published_at" type="datetime-local" value="{{ old('published_at', $newsPost->published_at?->format('Y-m-d\TH:i')) }}"></div>
                            <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit">Update post</button></div>
                        </form>
                        <form method="POST" action="{{ route('admin.clubs.news.destroy', [$club, $newsPost]) }}" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-secondary" type="submit">Delete</button>
                        </form>
                    </div>
                @empty
                    <div class="content-panel p-4 p-lg-5 text-center">
                        <p class="text-secondary mb-0">No news posts yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
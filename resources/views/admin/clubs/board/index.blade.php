@extends('layouts.app', ['title' => 'Club Board | KlubbManager'])

@section('content')
    <div class="row g-4 g-xl-5 align-items-start">
        <div class="col-xl-4">
            <div class="content-panel p-4 p-md-5 position-sticky" style="top: 6rem;">
                <div class="section-label mb-2">Administration</div>
                <h1 class="h2 fw-bold mb-3">Manage public board information</h1>
                <p class="text-secondary mb-4">Publish board and official roles separately from private membership records.</p>
                <a class="btn btn-outline-primary w-100" href="{{ route('admin.clubs.edit', $club) }}">Back to club</a>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="content-panel p-4 p-lg-5 mb-4">
                <div class="section-label mb-2">New board entry</div>
                <h2 class="h3 fw-bold mb-4">Create public contact</h2>
                <form method="POST" action="{{ route('admin.clubs.board.store', $club) }}" class="row g-3">
                    @csrf
                    <div class="col-md-6"><input class="form-control" name="name" type="text" value="{{ old('name') }}" placeholder="Name" required></div>
                    <div class="col-md-6"><input class="form-control" name="title" type="text" value="{{ old('title') }}" placeholder="Role title" required></div>
                    <div class="col-md-6"><input class="form-control" name="email" type="email" value="{{ old('email') }}" placeholder="Email"></div>
                    <div class="col-md-6"><input class="form-control" name="phone" type="text" value="{{ old('phone') }}" placeholder="Phone"></div>
                    <div class="col-md-4"><input class="form-control" name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" placeholder="Sort order"></div>
                    <div class="col-12"><textarea class="form-control" name="bio" rows="4" placeholder="Short biography or description">{{ old('bio') }}</textarea></div>
                    <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" id="board_is_public" name="is_public" type="checkbox" value="1" @checked(old('is_public', true))><label class="form-check-label" for="board_is_public">Visible on public pages</label></div></div>
                    <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit">Save board entry</button></div>
                </form>
            </div>
            <div class="vstack gap-3">
                @forelse ($boardMembers as $boardMember)
                    <div class="content-panel p-4 p-lg-5">
                        <form method="POST" action="{{ route('admin.clubs.board.update', [$club, $boardMember]) }}" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6"><input class="form-control" name="name" type="text" value="{{ old('name', $boardMember->name) }}" required></div>
                            <div class="col-md-6"><input class="form-control" name="title" type="text" value="{{ old('title', $boardMember->title) }}" required></div>
                            <div class="col-md-6"><input class="form-control" name="email" type="email" value="{{ old('email', $boardMember->email) }}"></div>
                            <div class="col-md-6"><input class="form-control" name="phone" type="text" value="{{ old('phone', $boardMember->phone) }}"></div>
                            <div class="col-md-4"><input class="form-control" name="sort_order" type="number" min="0" value="{{ old('sort_order', $boardMember->sort_order) }}"></div>
                            <div class="col-12"><textarea class="form-control" name="bio" rows="4">{{ old('bio', $boardMember->bio) }}</textarea></div>
                            <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" id="board-public-{{ $boardMember->id }}" name="is_public" type="checkbox" value="1" @checked(old('is_public', $boardMember->is_public))><label class="form-check-label" for="board-public-{{ $boardMember->id }}">Visible on public pages</label></div></div>
                            <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary" type="submit">Update board entry</button></div>
                        </form>
                        <form method="POST" action="{{ route('admin.clubs.board.destroy', [$club, $boardMember]) }}" class="mt-3">@csrf @method('DELETE')<button class="btn btn-outline-secondary" type="submit">Delete</button></form>
                    </div>
                @empty
                    <div class="content-panel p-4 p-lg-5 text-center"><p class="text-secondary mb-0">No public board entries yet.</p></div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
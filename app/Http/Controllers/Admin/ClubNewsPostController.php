<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesClubAdministration;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClubNewsPostRequest;
use App\Models\Club;
use App\Models\ClubNewsPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubNewsPostController extends Controller
{
    use AuthorizesClubAdministration;

    public function index(Request $request, Club $club): View
    {
        $this->ensureCanAdministerClub($request, $club);

        return view('admin.clubs.news.index', [
            'club' => $club,
            'newsPosts' => $club->newsPosts()->latest('published_at')->latest('created_at')->get(),
        ]);
    }

    public function store(ClubNewsPostRequest $request, Club $club): RedirectResponse
    {
        $club->newsPosts()->create($request->validated());

        return redirect()->route('club-admin.clubs.news.index', $club)->with('status', 'News post saved.');
    }

    public function update(ClubNewsPostRequest $request, Club $club, ClubNewsPost $newsPost): RedirectResponse
    {
        $item = $this->newsPost($club, $newsPost);
        $item->update($request->validated());

        return redirect()->route('club-admin.clubs.news.index', $club)->with('status', 'News post updated.');
    }

    public function destroy(Club $club, ClubNewsPost $newsPost): RedirectResponse
    {
        $item = $this->newsPost($club, $newsPost);
        $item->delete();

        return redirect()->route('club-admin.clubs.news.index', $club)->with('status', 'News post deleted.');
    }

    private function newsPost(Club $club, ClubNewsPost $newsPost): ClubNewsPost
    {
        abort_unless($newsPost->club->is($club), 404);

        return $newsPost;
    }
}
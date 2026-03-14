<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubEventRequest;
use App\Models\Club;
use App\Models\ClubEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClubEventController extends Controller
{
    public function index(Club $club): View
    {
        return view('admin.clubs.events.index', [
            'club' => $club,
            'events' => $club->events()->orderBy('starts_at')->get(),
        ]);
    }

    public function store(ClubEventRequest $request, Club $club): RedirectResponse
    {
        $club->events()->create($request->validated());

        return redirect()->route('admin.clubs.events.index', $club)->with('status', 'Event saved.');
    }

    public function update(ClubEventRequest $request, Club $club, ClubEvent $event): RedirectResponse
    {
        $item = $this->event($club, $event);
        $item->update($request->validated());

        return redirect()->route('admin.clubs.events.index', $club)->with('status', 'Event updated.');
    }

    public function destroy(Club $club, ClubEvent $event): RedirectResponse
    {
        $item = $this->event($club, $event);
        $item->delete();

        return redirect()->route('admin.clubs.events.index', $club)->with('status', 'Event deleted.');
    }

    private function event(Club $club, ClubEvent $event): ClubEvent
    {
        abort_unless($event->club->is($club), 404);

        return $event;
    }
}
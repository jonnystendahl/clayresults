<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingResultRequest;
use App\Models\Club;
use App\Models\Member;
use App\Models\TrainingResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingResultController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Member $member */
        $member = $request->user();
        $activeClub = $member->mainClub;

        $trainingResults = $member
            ->trainingResults()
            ->when($activeClub instanceof Club, fn ($query) => $query->where('club_id', $activeClub->id))
            ->latest('performed_on')
            ->latest('created_at')
            ->get();

        return view('training-results.index', [
            'activeClub' => $activeClub,
            'disciplines' => TrainingResult::DISCIPLINES,
            'trainingResults' => $trainingResults,
            'stats' => [
                'sessions' => $trainingResults->count(),
                'bestScore' => (int) ($trainingResults->max('score') ?? 0),
                'averageScore' => round((float) ($trainingResults->avg('score') ?? 0), 1),
            ],
        ]);
    }

    public function store(TrainingResultRequest $request): RedirectResponse
    {
        /** @var Member $member */
        $member = $request->user();
        $membership = $member->mainClubMembership();

        if ($membership === null) {
            return redirect()
                ->route('training-results.index')
                ->withErrors(['club' => 'Choose a main club before saving results.']);
        }

        $member->trainingResults()->create([
            ...$request->validated(),
            'club_id' => $membership->club_id,
        ]);

        return redirect()
            ->route('training-results.index')
            ->with('status', 'Training result saved.');
    }

    public function edit(Request $request, TrainingResult $trainingResult): View
    {
        return view('training-results.edit', [
            'disciplines' => TrainingResult::DISCIPLINES,
            'trainingResult' => $this->ownedResult($request, $trainingResult),
        ]);
    }

    public function update(TrainingResultRequest $request, TrainingResult $trainingResult): RedirectResponse
    {
        $this->ownedResult($request, $trainingResult)->update($request->validated());

        return redirect()
            ->route('training-results.index')
            ->with('status', 'Training result updated.');
    }

    public function destroy(Request $request, TrainingResult $trainingResult): RedirectResponse
    {
        $this->ownedResult($request, $trainingResult)->delete();

        return redirect()
            ->route('training-results.index')
            ->with('status', 'Training result deleted.');
    }

    private function ownedResult(Request $request, TrainingResult $trainingResult): TrainingResult
    {
        /** @var Member $member */
        $member = $request->user();

        abort_unless($trainingResult->member->is($member), 404);

        if ($trainingResult->club !== null) {
            abort_unless($member->canAccessClub($trainingResult->club), 404);
        }

        return $trainingResult;
    }
}
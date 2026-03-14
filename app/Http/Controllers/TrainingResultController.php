<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingResultRequest;
use App\Models\TrainingResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingResultController extends Controller
{
    public function index(Request $request): View
    {
        $trainingResults = $request->user()
            ->trainingResults()
            ->latest('performed_on')
            ->latest('created_at')
            ->get();

        return view('training-results.index', [
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
        $request->user()->trainingResults()->create($request->validated());

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
        abort_unless($trainingResult->user->is($request->user()), 404);

        return $trainingResult;
    }
}
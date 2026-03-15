<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClubRenewalDecisionRequest;
use App\Http\Requests\ClubRenewalSettingRequest;
use App\Models\Club;
use App\Models\ClubRenewalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClubRenewalController extends Controller
{
    public function edit(Club $club): View
    {
        return view('admin.clubs.renewal.edit', [
            'club' => $club,
            'renewalSetting' => $club->renewalSetting()->firstOrNew(),
            'renewalRequests' => $club->renewalRequests()->with(['membership.member'])->latest('submitted_at')->get(),
        ]);
    }

    public function update(ClubRenewalSettingRequest $request, Club $club): RedirectResponse
    {
        $club->renewalSetting()->updateOrCreate([], $request->validated());

        return redirect()->route('admin.clubs.renewal.edit', $club)->with('status', 'Renewal settings updated.');
    }

    public function updateRequest(ClubRenewalDecisionRequest $request, Club $club, ClubRenewalRequest $renewalRequest): RedirectResponse
    {
        $item = $this->renewalRequest($club, $renewalRequest);
        $validated = $request->validated();
        $validated['decided_at'] = $validated['status'] === 'pending' ? null : now();

        $item->update($validated);

        return redirect()->route('admin.clubs.renewal.edit', $club)->with('status', 'Renewal request updated.');
    }

    private function renewalRequest(Club $club, ClubRenewalRequest $renewalRequest): ClubRenewalRequest
    {
        abort_unless($renewalRequest->club->is($club), 404);

        return $renewalRequest;
    }
}
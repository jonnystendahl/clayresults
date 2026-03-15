<?php

namespace App\Http\Requests;

use App\Models\Club;
use App\Models\ClubMembership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_club_admin' => $this->boolean('is_club_admin'),
            'is_paid' => $this->boolean('is_paid'),
        ]);
    }

    /**
     * @return array<string, array<int, \\Illuminate\\Contracts\\Validation\\ValidationRule|string>>
     */
    public function rules(): array
    {
        /** @var Club $club */
        $club = $this->route('club');
        /** @var ClubMembership|null $clubMembership */
        $clubMembership = $this->route('clubMembership');

        return [
            'member_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
                Rule::unique('club_memberships', 'member_id')
                    ->where(fn ($query) => $query->where('club_id', $club->id))
                    ->ignore($clubMembership),
            ],
            'role' => ['required', 'string', 'max:100'],
            'is_club_admin' => ['required', 'boolean'],
            'is_paid' => ['required', 'boolean'],
            'joined_on' => ['required', 'date'],
            'last_paid_on' => ['nullable', 'date', 'after_or_equal:joined_on'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:joined_on'],
        ];
    }
}
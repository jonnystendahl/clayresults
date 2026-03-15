<?php

namespace App\Http\Requests;

use App\Models\Club;
use App\Models\ClubMembership;
use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Club $club */
        $club = $this->route('club');

        return $this->user()?->canAdministerClub($club) ?? false;
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

        if ($clubMembership === null) {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    function (string $attribute, mixed $value, \Closure $fail) use ($club): void {
                        $memberId = Member::query()->where('email', $value)->value('id');

                        if ($memberId !== null && ClubMembership::query()->where('club_id', $club->id)->where('member_id', $memberId)->exists()) {
                            $fail('This member already belongs to the club.');
                        }
                    },
                ],
                'role' => ['required', 'string', 'max:100'],
                'is_club_admin' => ['required', 'boolean'],
                'is_paid' => ['required', 'boolean'],
                'joined_on' => ['required', 'date'],
                'last_paid_on' => ['nullable', 'date', 'after_or_equal:joined_on'],
                'ends_on' => ['nullable', 'date', 'after_or_equal:joined_on'],
            ];
        }

        return [
            'role' => ['required', 'string', 'max:100'],
            'is_club_admin' => ['required', 'boolean'],
            'is_paid' => ['required', 'boolean'],
            'joined_on' => ['required', 'date'],
            'last_paid_on' => ['nullable', 'date', 'after_or_equal:joined_on'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:joined_on'],
        ];
    }
}
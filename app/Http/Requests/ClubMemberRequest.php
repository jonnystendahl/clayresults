<?php

namespace App\Http\Requests;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAdministerClub($this->route('club')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_admin')) {
            $this->merge([
                'is_admin' => $this->boolean('is_admin'),
            ]);
        }
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public function rules(): array
    {
        /** @var Member $member */
        $member = $this->route('member');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(Member::class)->ignore($member)],
        ];

        if ($this->user()?->isAdmin()) {
            $rules['is_admin'] = ['required', 'boolean'];
        }

        return $rules;
    }
}
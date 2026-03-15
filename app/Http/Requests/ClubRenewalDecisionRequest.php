<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubRenewalDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAdministerClub($this->route('club')) ?? false;
    }

    /**
     * @return array<string, array<int, \\Illuminate\\Contracts\\Validation\\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
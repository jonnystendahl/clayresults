<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubEventRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description' => ['required', 'string', 'max:10000'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
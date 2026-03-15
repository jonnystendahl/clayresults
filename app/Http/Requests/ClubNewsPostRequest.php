<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubNewsPostRequest extends FormRequest
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
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:10000'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
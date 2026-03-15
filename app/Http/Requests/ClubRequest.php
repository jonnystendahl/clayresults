<?php

namespace App\Http\Requests;

use App\Models\Club;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        $club = $this->route('club');

        if ($club === null) {
            return $this->user()?->isAdmin() ?? false;
        }

        return $this->user()?->canAdministerClub($club) ?? false;
    }

    /**
     * @return array<string, array<int, \\Illuminate\\Contracts\\Validation\\ValidationRule|string>>
     */
    public function rules(): array
    {
        /** @var Club|null $club */
        $club = $this->route('club');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique(Club::class)->ignore($club)],
            'address' => ['nullable', 'string', 'max:255'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_email' => ['nullable', 'email', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
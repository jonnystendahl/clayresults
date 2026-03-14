<?php

namespace App\Http\Requests;

use App\Models\Club;
use Illuminate\Foundation\Http\FormRequest;

class StoreClubRenewalRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Club $club */
        $club = $this->route('club');

        return $this->user()?->canAccessClub($club) ?? false;
    }

    /**
     * @return array<string, array<int, \\Illuminate\\Contracts\\Validation\\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
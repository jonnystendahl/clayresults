<?php

namespace App\Http\Requests;

use App\Models\Club;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreClubMemberTemporaryPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Club $club */
        $club = $this->route('club');

        return $this->user()?->canAdministerClub($club) ?? false;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
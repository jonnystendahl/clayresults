<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubRenewalSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_open' => $this->boolean('is_open'),
        ]);
    }

    /**
     * @return array<string, array<int, \\Illuminate\\Contracts\\Validation\\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'season_label' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:4000'],
            'fee_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'fee_currency' => ['nullable', 'string', 'max:8'],
            'renewal_deadline' => ['nullable', 'date'],
            'payment_details' => ['nullable', 'string', 'max:4000'],
            'is_open' => ['required', 'boolean'],
        ];
    }
}
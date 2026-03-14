<?php

namespace App\Http\Requests;

use App\Models\TrainingResult;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrainingResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|array|string>>
     */
    public function rules(): array
    {
        return [
            'performed_on' => ['required', 'date'],
            'discipline' => ['required', 'string', Rule::in(TrainingResult::DISCIPLINES)],
            'score' => ['required', 'integer', 'min:0', 'max:999'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'alternative_ids' => ['nullable', 'array'],
            'alternative_ids.*' => ['integer', 'exists:alternatives,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul assessment wajib diisi.',
            'title.max' => 'Judul maksimal 200 karakter.',
            'alternative_ids.*.exists' => 'Salah satu alternatif tidak ditemukan.',
        ];
    }
}

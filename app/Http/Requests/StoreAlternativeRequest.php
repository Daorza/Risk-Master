<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAlternativeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama alternatif wajib diisi.',
            'name.string' => 'Nama alternatif harus berupa string.',
            'name.max' => 'Nama maksimal 150 karakter.',
            'description.string' => 'Deskripsi harus berupa string.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
        ];
    }
}

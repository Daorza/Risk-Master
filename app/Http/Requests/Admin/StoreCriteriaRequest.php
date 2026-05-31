<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCriteriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:criteria'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['benefit', 'cost'])],
            'weight' => ['required', 'numeric', 'min:0.0001', 'max:1'],
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'Nama kriteria wajib diisi.',
            'name.unique' => 'Nama kriteria sudah ada.',
            'type.required' => 'Tipe kriteria wajib diisi.',
            'type.in' => 'Tipe kriteria tidak valid.',
            'weight.required' => 'Bobot kriteria wajib diisi.',
            'weight.numeric' => 'Bobot kriteria harus berupa angka.',
            'weight.min' => 'Bobot kriteria minimal adalah 0.0001.',
            'weight.max' => 'Bobot kriteria maksimal adalah 1.',
        ];
    }
}

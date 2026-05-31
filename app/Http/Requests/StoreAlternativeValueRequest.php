<?php

namespace App\Http\Requests;

use App\Models\Assessment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAlternativeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Assessment $assessment */
        $assessment = $this->route('assessment');
        $user = $this->user();

        return $user->role === 'admin' || $assessment->user_id === $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'values' => ['required', 'array', 'min:1'],
            'values.*.alternative_id' => ['required', 'integer', 'exists:alternatives,id'],
            'values.*.criteria_id' => ['required', 'integer', 'exists:criteria,id'],
            'values.*.value' => ['required', 'numeric', 'min:0', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'values.required' => 'Data tidak boleh kosong.',
            'values.*alternative_id.required' => 'ID alternatif wajib ada.',
            'values.*alternative_id.exists' => 'Alternatif tidak ditemukan',
            'values.*criteria_id.required' => 'ID kriteria wajib ada.',
            'values.*criteria_id.exists' => 'Kriteria tidak ditemukan',
            'values.*value.required' => 'Nilai wajib diisi.',
            'values.*value.numeric' => 'Nilai harus berupa angka.',
            'values.*value.min' => 'Nilai minimal adalah 0.',
            'values.*value.max' => 'Nilai maksimal adalah 10.',
        ];
    }

    protected function failedAuthorization(): never
    {
        abort(403, 'Anda tidak punya akses ke assessment ini.');
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCriteriaRequest extends FormRequest
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
        $criteriaId = $this->route('criteria')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('criteria')->ignore($criteriaId)],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', Rule::in(['benefit', 'cost'])],
            'weight' => ['sometimes', 'numeric', 'min:0.0001', 'max:1'],
        ];
    }
}

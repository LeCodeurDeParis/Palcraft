<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaletteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'is_public' => ['sometimes', 'boolean'],
            'blocks' => ['sometimes', 'array', 'size:5'],
            'blocks.*' => ['required', 'integer', 'distinct', 'exists:blocks,id'],
        ];
    }
}

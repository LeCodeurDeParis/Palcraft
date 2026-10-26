<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePaletteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_public' => ['boolean'],
            'blocks' => ['required', 'array', 'size:5'],
            'blocks.*' => ['required', 'integer', 'distinct', 'exists:blocks,id'],
        ];
    }
}

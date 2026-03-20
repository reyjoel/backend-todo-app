<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'q' => ['nullable', 'string', 'max:255'],
        ];
    }
}
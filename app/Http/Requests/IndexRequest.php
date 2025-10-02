<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class IndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $perPage = $this->query('per_page', 12);

        $this->merge(['per_page' => is_numeric($perPage) ? (int) $perPage : 12]);
    }

    public function perPage(): int
    {
        return (int) $this->input('per_page', 12);
    }
}

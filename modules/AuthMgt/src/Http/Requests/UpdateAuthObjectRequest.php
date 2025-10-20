<?php

namespace Modules\AuthMgt\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAuthObjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['sometimes', 'string', Rule::unique('auth_objects')->ignore($this->auth_object)],
            'module' => 'sometimes|string',
            'description' => 'nullable|string',
            'actions' => 'sometimes|array',
            'status' => 'sometimes|string',
        ];
    }
}
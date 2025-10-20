<?php

namespace Modules\AuthMgt\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuthObjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:auth_objects,code',
            'module' => 'required|string',
            'description' => 'nullable|string',
            'actions' => 'required|array',
            'status' => 'sometimes|string',
        ];
    }
}
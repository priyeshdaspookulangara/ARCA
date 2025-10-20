<?php

namespace Modules\AuthMgt\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For now, we'll allow anyone to create a role.
        // In a real application, this would be protected by a permission.
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'scope' => 'sometimes|string|in:global,module',
            'parent_id' => 'nullable|exists:roles,id',
        ];
    }
}
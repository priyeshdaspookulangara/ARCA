<?php

namespace Modules\AuthMgt\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For now, we'll allow anyone to update a role.
        // In a real application, this would be protected by a permission.
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', Rule::unique('roles')->ignore($this->role)],
            'description' => 'nullable|string',
            'scope' => 'sometimes|string|in:global,module',
            'parent_id' => 'nullable|exists:roles,id',
        ];
    }
}
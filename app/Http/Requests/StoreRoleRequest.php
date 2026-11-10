<?php

namespace App\Http\Requests;

use App\Support\PermissionTree;
use Database\Seeders\PermissionSeeder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where('guard_name', 'web'),
                Rule::notIn([PermissionSeeder::ADMIN_ROLE]),
            ],
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', PermissionTree::keys())],
        ];
    }
}

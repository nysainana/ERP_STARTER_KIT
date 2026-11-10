<?php

namespace App\Http\Requests;

use App\Support\PermissionTree;
use Database\Seeders\PermissionSeeder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateRoleRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'in:'.implode(',', PermissionTree::keys())],
        ];
    }

    /**
     * Le rôle Administrateur garde toujours l'intégralité des permissions,
     * sinon il pourrait se verrouiller hors de l'application.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $role = $this->route('role');

            if ($role?->name === PermissionSeeder::ADMIN_ROLE) {
                $submitted = collect($this->input('permissions', []))->unique()->sort()->values();
                $expected = collect(PermissionTree::keys())->sort()->values();

                if ($submitted->all() !== $expected->all()) {
                    $validator->errors()->add('permissions', 'Le rôle Administrateur conserve toutes les permissions.');
                }
            }

            // Un utilisateur ne peut pas se retirer à lui-même la gestion des rôles :
            // si le rôle modifié est l'un des siens, roles.manage doit rester coché.
            $user = $this->user();
            $ownsRole = $role !== null && $user?->hasRole($role->name);
            $keepsManage = in_array('roles.manage', (array) $this->input('permissions', []), true);

            if ($ownsRole && ! $keepsManage && $user->hasPermissionTo('roles.manage')) {
                $validator->errors()->add('permissions', 'Vous ne pouvez pas retirer la gestion des rôles à votre propre rôle.');
            }
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Support\PermissionTree;
use Database\Seeders\PermissionSeeder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Liste des rôles et arbre de permissions à cocher.
     */
    public function index(): Response
    {
        return Inertia::render('roles/index', [
            'tree' => PermissionTree::tree(),
            'roles' => Role::with('permissions:id,name')
                ->orderBy('name')
                ->get()
                ->map(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'protected' => $role->name === PermissionSeeder::ADMIN_ROLE,
                    'permissions' => $role->permissions->pluck('name')->values(),
                ]),
        ]);
    }

    /**
     * Crée un rôle avec ses permissions.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create([
            'name' => $request->validated('name'),
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->validated('permissions'));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Rôle créé.']);

        return to_route('roles.index');
    }

    /**
     * Supprime un rôle non protégé et non attribué à des utilisateurs.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === PermissionSeeder::ADMIN_ROLE) {
            abort(422, 'Le rôle Administrateur ne peut pas être supprimé.');
        }

        if ($role->users()->exists()) {
            abort(422, 'Ce rôle est encore attribué à des utilisateurs.');
        }

        $role->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Rôle supprimé.']);

        return to_route('roles.index');
    }

    /**
     * Met à jour les permissions d'un rôle.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->syncPermissions($request->validated('permissions'));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Rôle mis à jour.']);

        return to_route('roles.index');
    }
}

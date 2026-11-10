<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Liste paginée des utilisateurs, avec recherche par nom ou e-mail.
     */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->with('roles:id,name')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->only(['id', 'name']),
            ]);

        return Inertia::render('users/index', [
            'users' => $users,
            'filters' => ['search' => $search],
            'currentUserId' => (int) $request->user()->id,
            'roles' => Role::orderBy('name')->get(['id', 'name'])
                ->map(fn (Role $role) => ['id' => $role->id, 'name' => $role->name]),
        ]);
    }

    /**
     * Crée un utilisateur avec son rôle unique.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();
        $user->syncRoles(Role::findOrFail($request->validated('role_id')));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Utilisateur créé.']);

        return to_route('users.index');
    }

    /**
     * Met à jour les informations et le rôle d'un utilisateur.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = [
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);
        $user->syncRoles(Role::findOrFail($request->validated('role_id')));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Utilisateur mis à jour.']);

        return to_route('users.index');
    }

    /**
     * Supprime un utilisateur. Impossible de supprimer son propre compte.
     */
    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(request()->user()), 422, 'Vous ne pouvez pas supprimer votre propre compte.');

        $user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Utilisateur supprimé.']);

        return to_route('users.index');
    }
}

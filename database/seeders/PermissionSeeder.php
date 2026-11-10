<?php

namespace Database\Seeders;

use App\Support\PermissionTree;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public const ADMIN_ROLE = 'Administrateur';

    /**
     * Synchronise la base avec config/permission-tree.php. Idempotent :
     * relançable à chaque déploiement.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $desired = PermissionTree::keys();

        foreach ($desired as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Supprime les permissions retirées de l'arbre (et leurs liens aux rôles).
        Permission::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', $desired)
            ->delete();

        $admin = Role::firstOrCreate(['name' => self::ADMIN_ROLE, 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

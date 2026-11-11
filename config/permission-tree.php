<?php

/*
|--------------------------------------------------------------------------
| Arbre des permissions (source de vérité)
|--------------------------------------------------------------------------
|
| Chaque feuille est une permission : `key` est son nom stocké en base
| (Spatie Permission), `routes` la liste des noms de routes qu'elle
| autorise. Les groupes (`is_group`) ne servent qu'à l'affichage.
|
| Pour ajouter une permission : ajoutez une feuille ici, puis lancez
| `php artisan db:seed --class=PermissionSeeder`. Rien d'autre à changer.
|
*/

return [
    [
        'key' => 'dashboard',
        'label' => 'Tableau de bord',
        'is_group' => true,
        'children' => [
            [
                'key' => 'dashboard.view',
                'label' => 'Voir le tableau de bord',
                'routes' => ['dashboard'],
            ],
        ],
    ],
    [
        'key' => 'users',
        'label' => 'Utilisateurs',
        'is_group' => true,
        'children' => [
            [
                'key' => 'users.view',
                'label' => 'Voir les utilisateurs',
                'routes' => ['users.index', 'users.show'],
            ],
            [
                'key' => 'users.create',
                'label' => 'Créer les utilisateurs',
                'routes' => ['users.create', 'users.store'],
            ],
            [
                'key' => 'users.update',
                'label' => 'Modifier les utilisateurs',
                'routes' => ['users.edit', 'users.update'],
            ],
            [
                'key' => 'users.delete',
                'label' => 'Supprimer les utilisateurs',
                'routes' => ['users.destroy'],
            ],
        ],
    ],
    [
        'key' => 'roles',
        'label' => 'Rôles',
        'is_group' => true,
        'children' => [
            [
                'key' => 'roles.view',
                'label' => 'Voir les rôles',
                'routes' => ['roles.index', 'roles.show'],
            ],
            [
                'key' => 'roles.create',
                'label' => 'Créer les rôles',
                'routes' => ['roles.create', 'roles.store'],
            ],
            [
                'key' => 'roles.update',
                'label' => 'Modifier les rôles',
                'routes' => ['roles.edit', 'roles.update'],
            ],
            [
                'key' => 'roles.delete',
                'label' => 'Supprimer les rôles',
                'routes' => ['roles.destroy'],
            ],
        ],
    ],
];

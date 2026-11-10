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
                'key' => 'users.manage',
                'label' => 'Gérer les utilisateurs',
                'routes' => ['users.create', 'users.store', 'users.edit', 'users.update', 'users.destroy'],
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
                'key' => 'roles.manage',
                'label' => 'Gérer les rôles',
                'routes' => ['roles.create', 'roles.store', 'roles.edit', 'roles.update', 'roles.destroy'],
            ],
        ],
    ],
];

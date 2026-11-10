<?php

namespace App\Support;

/**
 * Lecture de l'arbre de permissions défini dans config/permission-tree.php.
 */
class PermissionTree
{
    /**
     * Arbre complet tel que défini dans la configuration.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function tree(): array
    {
        return config('permission-tree', []);
    }

    /**
     * Toutes les feuilles (permissions) aplaties.
     *
     * @return array<int, array{key: string, label: string, routes: array<int, string>}>
     */
    public static function leaves(?array $nodes = null): array
    {
        $leaves = [];

        foreach ($nodes ?? static::tree() as $node) {
            if (! empty($node['children'])) {
                $leaves = [...$leaves, ...static::leaves($node['children'])];

                continue;
            }

            if (isset($node['key'])) {
                $leaves[] = [
                    'key' => $node['key'],
                    'label' => $node['label'] ?? $node['key'],
                    'routes' => $node['routes'] ?? [],
                ];
            }
        }

        return $leaves;
    }

    /**
     * Clés de permission désirées (sans doublon).
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_values(array_unique(array_column(static::leaves(), 'key')));
    }

    /**
     * Noms de routes autorisés par une liste de permissions.
     *
     * @param  array<int, string>  $permissionNames
     * @return array<int, string>
     */
    public static function routesFor(array $permissionNames): array
    {
        $routes = [];

        foreach (static::leaves() as $leaf) {
            if (in_array($leaf['key'], $permissionNames, true)) {
                $routes = [...$routes, ...$leaf['routes']];
            }
        }

        return array_values(array_unique($routes));
    }
}

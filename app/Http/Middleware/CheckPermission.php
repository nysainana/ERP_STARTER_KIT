<?php

namespace App\Http\Middleware;

use App\Support\PermissionTree;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôle d'accès dynamique : l'accès à une route est accordé si l'une des
 * permissions de l'utilisateur autorise son nom de route (config/permission-tree.php).
 * Les routes sans nom ne sont pas contrôlées.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if ($routeName === null) {
            return $next($request);
        }

        $user = $request->user();
        $allowedRoutes = PermissionTree::routesFor($user?->getAllPermissionNames() ?? []);

        if (! in_array($routeName, $allowedRoutes, true)) {
            abort(403, 'Action non autorisée.');
        }

        return $next($request);
    }
}

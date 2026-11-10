import { usePage } from '@inertiajs/react';
import { useCallback, useMemo } from 'react';

/**
 * Permissions de l'utilisateur courant, partagées par Inertia
 * (HandleInertiaRequests). Le serveur reste l'autorité : ce hook ne
 * sert qu'à masquer les éléments d'interface.
 */
export function usePermissions() {
    const permissions = usePage().props.auth.permissions;

    const granted = useMemo(() => new Set(permissions ?? []), [permissions]);

    /** Vrai si l'utilisateur possède au moins une des permissions données. */
    const can = useCallback(
        (...required: string[]) => required.some((p) => granted.has(p)),
        [granted],
    );

    return { permissions, can };
}

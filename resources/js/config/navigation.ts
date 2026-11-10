import { LayoutGrid, Settings, Shield, Users } from 'lucide-react';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

export const mainNavItems: NavItem[] = [
    {
        title: 'Tableau de bord',
        href: dashboard(),
        icon: LayoutGrid,
        permission: 'dashboard.view',
    },
    {
        title: 'Utilisateurs',
        href: '/users',
        icon: Users,
        permission: 'users.view',
    },
    {
        title: 'Rôles',
        href: '/roles',
        icon: Shield,
        permission: 'roles.view',
    },
    {
        title: 'Paramètres',
        href: '/settings/profile',
        icon: Settings,
    },
];

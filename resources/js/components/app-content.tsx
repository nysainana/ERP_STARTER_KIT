import * as React from 'react';
import { SidebarInset } from '@/components/ui/sidebar';
import { cn } from '@/lib/utils';
import type { AppVariant } from '@/types';

type Props = React.ComponentProps<'main'> & {
    variant?: AppVariant;
};

// Animation d'entrée partagée par toutes les pages de l'application.
const enter = 'animate-in fade-in slide-in-from-bottom-2 duration-300 motion-reduce:animate-none';

// Entrée en cascade d'un élément de liste (ligne ou carte), selon son rang.
export function staggerEnter(index: number, step = 50) {
    return {
        className: `${enter} fill-mode-both`,
        style: { animationDelay: `${index * step}ms` },
    };
}

export function AppContent({ variant = 'sidebar', className, children, ...props }: Props) {
    if (variant === 'sidebar') {
        return (
            <SidebarInset className={cn(enter, className)} {...props}>
                {children}
            </SidebarInset>
        );
    }

    return (
        <main
            className={cn(
                'mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl',
                enter,
                className,
            )}
            {...props}
        >
            {children}
        </main>
    );
}

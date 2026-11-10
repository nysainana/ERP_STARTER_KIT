import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';

type EmptyStateProps = {
    icon?: LucideIcon;
    title: string;
    description?: string;
    action?: ReactNode;
};

export function EmptyState({
    icon: Icon,
    title,
    description,
    action,
}: EmptyStateProps) {
    return (
        <div className="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed p-10 text-center">
            {Icon && (
                <div className="flex size-12 items-center justify-center rounded-full bg-muted">
                    <Icon className="size-6 text-muted-foreground" />
                </div>
            )}
            <div className="space-y-1">
                <p className="font-medium">{title}</p>
                {description && (
                    <p className="text-sm text-muted-foreground">
                        {description}
                    </p>
                )}
            </div>
            {action}
        </div>
    );
}

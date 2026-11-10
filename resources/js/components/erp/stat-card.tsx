import type { LucideIcon } from 'lucide-react';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

type StatCardProps = {
    title: string;
    value: string | number;
    description?: string;
    icon?: LucideIcon;
};

export function StatCard({
    title,
    value,
    description,
    icon: Icon,
}: StatCardProps) {
    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardDescription>{title}</CardDescription>
                {Icon && <Icon className="size-4 text-muted-foreground" />}
            </CardHeader>
            <CardContent className="space-y-1">
                <CardTitle className="text-2xl font-semibold">
                    {value}
                </CardTitle>
                {description && (
                    <p className="text-xs text-muted-foreground">
                        {description}
                    </p>
                )}
            </CardContent>
        </Card>
    );
}

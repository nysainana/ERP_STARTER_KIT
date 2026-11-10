import { Head } from '@inertiajs/react';
import { Activity, Shield, Users } from 'lucide-react';
import { EmptyState } from '@/components/erp/empty-state';
import { PageHeader } from '@/components/erp/page-header';
import { StatCard } from '@/components/erp/stat-card';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

export default function Dashboard() {
    return (
        <>
            <Head title="Tableau de bord" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <PageHeader
                    title="Tableau de bord"
                    description="Vue d'ensemble de votre espace de gestion."
                />

                <div className="grid gap-4 md:grid-cols-3">
                    <StatCard
                        title="Utilisateurs"
                        value="—"
                        description="Les statistiques arriveront avec le module utilisateurs"
                        icon={Users}
                    />
                    <StatCard
                        title="Rôles"
                        value="—"
                        description="Les rôles seront gérés en phase 4"
                        icon={Shield}
                    />
                    <StatCard
                        title="Activité"
                        value="—"
                        description="Le journal d'activité sera branché ensuite"
                        icon={Activity}
                    />
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Activité récente</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <EmptyState
                            icon={Activity}
                            title="Aucune activité pour le moment"
                            description="Les actions effectuées dans l'application apparaîtront ici."
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Tableau de bord',
            href: dashboard(),
        },
    ],
};

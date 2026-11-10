import { Head, router } from '@inertiajs/react';
import { Lock, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { PageHeader } from '@/components/erp/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type PermissionLeaf = {
    key: string;
    label: string;
    routes: string[];
};

type PermissionGroup = {
    key: string;
    label: string;
    is_group?: boolean;
    children: PermissionLeaf[];
};

type Role = {
    id: number;
    name: string;
    protected: boolean;
    permissions: string[];
};

type Props = {
    tree: PermissionGroup[];
    roles: Role[];
};

export default function RolesIndex({ tree, roles }: Props) {
    return (
        <>
            <Head title="Rôles" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <PageHeader
                    title="Rôles"
                    description="Cochez les permissions accordées à chaque rôle."
                    actions={<CreateRoleDialog tree={tree} />}
                />

                <div className="grid gap-4 xl:grid-cols-2">
                    {roles.map((role) => (
                        <RoleCard key={role.id} role={role} tree={tree} />
                    ))}
                </div>
            </div>
        </>
    );
}

function PermissionCheckboxes({
    idPrefix,
    tree,
    selected,
    onToggle,
    disabled,
}: {
    idPrefix: string;
    tree: PermissionGroup[];
    selected: string[];
    onToggle: (key: string, checked: boolean) => void;
    disabled?: boolean;
}) {
    return (
        <>
            {tree.map((group) => (
                <div key={group.key} className="space-y-2">
                    <p className="text-sm font-medium">{group.label}</p>
                    <div className="grid gap-2 pl-2">
                        {group.children.map((leaf) => {
                            const id = `${idPrefix}-${leaf.key}`;
                            return (
                                <div key={leaf.key} className="flex items-center gap-2">
                                    <Checkbox
                                        id={id}
                                        checked={disabled || selected.includes(leaf.key)}
                                        disabled={disabled}
                                        onCheckedChange={(checked) =>
                                            onToggle(leaf.key, checked === true)
                                        }
                                    />
                                    <Label htmlFor={id} className="font-normal">
                                        {leaf.label}
                                    </Label>
                                </div>
                            );
                        })}
                    </div>
                </div>
            ))}
        </>
    );
}

function toggleIn(list: string[], key: string, checked: boolean): string[] {
    return checked ? [...list, key] : list.filter((item) => item !== key);
}

function CreateRoleDialog({ tree }: { tree: PermissionGroup[] }) {
    const [open, setOpen] = useState(false);
    const [name, setName] = useState('');
    const [selected, setSelected] = useState<string[]>([]);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const reset = () => {
        setName('');
        setSelected([]);
        setErrors({});
    };

    const submit = () => {
        setProcessing(true);
        router.post(
            '/roles',
            { name, permissions: selected },
            {
                preserveScroll: true,
                onError: (serverErrors) => setErrors(serverErrors as Record<string, string>),
                onSuccess: () => {
                    reset();
                    setOpen(false);
                },
                onFinish: () => setProcessing(false),
            },
        );
    };

    return (
        <Dialog
            open={open}
            onOpenChange={(value) => {
                setOpen(value);
                if (!value) reset();
            }}
        >
            <Button onClick={() => setOpen(true)}>
                <Plus />
                Nouveau rôle
            </Button>
            <DialogContent className="max-h-[85vh] overflow-y-auto">
                <DialogTitle>Nouveau rôle</DialogTitle>
                <DialogDescription>
                    Donnez un nom au rôle et cochez ses permissions.
                </DialogDescription>

                <div className="grid gap-2">
                    <Label htmlFor="role-name">Nom</Label>
                    <Input
                        id="role-name"
                        value={name}
                        onChange={(event) => setName(event.target.value)}
                        required
                    />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>

                <div className="space-y-4">
                    <PermissionCheckboxes
                        idPrefix="new-role"
                        tree={tree}
                        selected={selected}
                        onToggle={(key, checked) => setSelected((current) => toggleIn(current, key, checked))}
                    />
                    {errors.permissions && (
                        <p className="text-sm text-destructive">{errors.permissions}</p>
                    )}
                </div>

                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Annuler</Button>
                    </DialogClose>
                    <Button onClick={submit} disabled={processing || name.trim() === ''}>
                        Créer
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

function RoleCard({ role, tree }: { role: Role; tree: PermissionGroup[] }) {
    const [selected, setSelected] = useState<string[]>(role.permissions);
    const [processing, setProcessing] = useState(false);
    const [confirmDelete, setConfirmDelete] = useState(false);
    const readOnly = role.protected;

    const save = () => {
        setProcessing(true);
        router.patch(
            `/roles/${role.id}`,
            { permissions: selected },
            {
                preserveScroll: true,
                onFinish: () => setProcessing(false),
            },
        );
    };

    const remove = () => {
        router.delete(`/roles/${role.id}`, {
            preserveScroll: true,
            onFinish: () => setConfirmDelete(false),
        });
    };

    return (
        <Card>
            <CardHeader>
                <div className="flex items-center justify-between gap-2">
                    <CardTitle className="flex items-center gap-2">
                        {role.name}
                        {readOnly && (
                            <Badge variant="secondary">
                                <Lock className="size-3" />
                                Protégé
                            </Badge>
                        )}
                    </CardTitle>
                    {!readOnly && (
                        <Button
                            variant="ghost"
                            size="icon"
                            aria-label={`Supprimer le rôle ${role.name}`}
                            onClick={() => setConfirmDelete(true)}
                        >
                            <Trash2 />
                        </Button>
                    )}
                </div>
                <CardDescription>
                    {readOnly
                        ? 'Ce rôle conserve toutes les permissions.'
                        : `${selected.length} permission(s) accordée(s).`}
                </CardDescription>
            </CardHeader>

            <CardContent className="space-y-4">
                <PermissionCheckboxes
                    idPrefix={`role-${role.id}`}
                    tree={tree}
                    selected={selected}
                    disabled={readOnly}
                    onToggle={(key, checked) => setSelected((current) => toggleIn(current, key, checked))}
                />

                {!readOnly && (
                    <div className="flex justify-end">
                        <Button onClick={save} disabled={processing}>
                            Enregistrer
                        </Button>
                    </div>
                )}
            </CardContent>

            <Dialog open={confirmDelete} onOpenChange={setConfirmDelete}>
                <DialogContent>
                    <DialogTitle>Supprimer le rôle « {role.name} » ?</DialogTitle>
                    <DialogDescription>
                        Le rôle ne peut être supprimé que s'il n'est attribué à aucun utilisateur.
                    </DialogDescription>
                    <DialogFooter className="gap-2">
                        <DialogClose asChild>
                            <Button variant="secondary">Annuler</Button>
                        </DialogClose>
                        <Button variant="destructive" onClick={remove}>
                            Supprimer
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </Card>
    );
}

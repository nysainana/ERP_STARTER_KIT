import { Head, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { PageHeader } from '@/components/erp/page-header';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
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

type UserRow = {
    id: number;
    name: string;
    email: string;
    role: RoleOption | null;
};

type RoleOption = {
    id: number;
    name: string;
};

type Paginated<T> = {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
};

type Props = {
    users: Paginated<UserRow>;
    filters: { search: string };
    roles: RoleOption[];
    currentUserId: number;
};

export default function UsersIndex({ users, filters, roles, currentUserId }: Props) {
    const [search, setSearch] = useState(filters.search);

    const applySearch = (event: React.FormEvent) => {
        event.preventDefault();
        router.get('/users', { search: search.trim() || undefined }, { preserveState: true, replace: true });
    };

    return (
        <>
            <Head title="Utilisateurs" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <PageHeader
                    title="Utilisateurs"
                    description="Gérez les comptes et leurs rôles."
                    actions={<UserDialog roles={roles} />}
                />

                <Card>
                    <CardContent className="space-y-4">
                        <form onSubmit={applySearch} className="flex gap-2">
                            <Input
                                placeholder="Rechercher par nom ou e-mail"
                                value={search}
                                onChange={(event) => setSearch(event.target.value)}
                                className="max-w-sm"
                            />
                            <Button type="submit" variant="secondary">
                                Rechercher
                            </Button>
                        </form>

                        <div className="overflow-x-auto rounded-md border">
                            <table className="w-full text-sm">
                                <thead className="bg-muted/50 text-left">
                                    <tr>
                                        <th className="px-3 py-2 font-medium">Nom</th>
                                        <th className="px-3 py-2 font-medium">E-mail</th>
                                        <th className="px-3 py-2 font-medium">Rôle</th>
                                        <th className="px-3 py-2 text-right font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {users.data.length === 0 && (
                                        <tr>
                                            <td colSpan={4} className="px-3 py-6 text-center text-muted-foreground">
                                                Aucun utilisateur trouvé.
                                            </td>
                                        </tr>
                                    )}
                                    {users.data.map((user) => (
                                        <tr key={user.id} className="border-t">
                                            <td className="px-3 py-2">{user.name}</td>
                                            <td className="px-3 py-2">{user.email}</td>
                                            <td className="px-3 py-2">
                                                {user.role && <Badge variant="secondary">{user.role.name}</Badge>}
                                            </td>
                                            <td className="px-3 py-2">
                                                <div className="flex justify-end gap-1">
                                                    <UserDialog roles={roles} user={user} />
                                                    <DeleteUserButton
                                                        user={user}
                                                        disabled={user.id === currentUserId}
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        <nav className="flex flex-wrap justify-end gap-1">
                            {users.links.map((link) => (
                                <Button
                                    key={link.label}
                                    variant={link.active ? 'default' : 'outline'}
                                    size="sm"
                                    disabled={!link.url}
                                    onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </nav>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

function UserDialog({ roles, user }: { roles: RoleOption[]; user?: UserRow }) {
    const editing = user !== undefined;
    const [open, setOpen] = useState(false);
    const [name, setName] = useState(user?.name ?? '');
    const [email, setEmail] = useState(user?.email ?? '');
    const [password, setPassword] = useState('');
    const [passwordConfirmation, setPasswordConfirmation] = useState('');
    const [roleId, setRoleId] = useState<number | null>(user?.role?.id ?? null);
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const reset = () => {
        setName(user?.name ?? '');
        setEmail(user?.email ?? '');
        setPassword('');
        setPasswordConfirmation('');
        setRoleId(user?.role?.id ?? null);
        setErrors({});
    };

    const submit = () => {
        setProcessing(true);
        const payload = {
            name,
            email,
            password,
            password_confirmation: passwordConfirmation,
            role_id: roleId,
        };
        const options = {
            preserveScroll: true,
            onError: (serverErrors: Record<string, string>) => setErrors(serverErrors),
            onSuccess: () => {
                setOpen(false);
            },
            onFinish: () => setProcessing(false),
        };

        if (editing) {
            router.patch(`/users/${user.id}`, payload, options);
        } else {
            router.post('/users', payload, options);
        }
    };

    return (
        <Dialog
            open={open}
            onOpenChange={(value) => {
                setOpen(value);
                if (!value) reset();
            }}
        >
            {editing ? (
                <Button variant="ghost" size="icon" aria-label={`Modifier ${user.name}`} onClick={() => setOpen(true)}>
                    <Pencil />
                </Button>
            ) : (
                <Button onClick={() => setOpen(true)}>
                    <Plus />
                    Nouvel utilisateur
                </Button>
            )}
            <DialogContent className="max-h-[85vh] overflow-y-auto">
                <DialogTitle>{editing ? 'Modifier l’utilisateur' : 'Nouvel utilisateur'}</DialogTitle>
                <DialogDescription>
                    {editing
                        ? 'Laissez le mot de passe vide pour le conserver.'
                        : 'Le compte est créé avec son adresse e-mail déjà vérifiée.'}
                </DialogDescription>

                <div className="grid gap-2">
                    <Label htmlFor="user-name">Nom</Label>
                    <Input id="user-name" value={name} onChange={(event) => setName(event.target.value)} required />
                    {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="user-email">E-mail</Label>
                    <Input
                        id="user-email"
                        type="email"
                        value={email}
                        onChange={(event) => setEmail(event.target.value)}
                        required
                    />
                    {errors.email && <p className="text-sm text-destructive">{errors.email}</p>}
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="user-password">Mot de passe</Label>
                    <Input
                        id="user-password"
                        type="password"
                        value={password}
                        onChange={(event) => setPassword(event.target.value)}
                        required={!editing}
                    />
                    {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="user-password-confirmation">Confirmer le mot de passe</Label>
                    <Input
                        id="user-password-confirmation"
                        type="password"
                        value={passwordConfirmation}
                        onChange={(event) => setPasswordConfirmation(event.target.value)}
                        required={!editing}
                    />
                </div>

                <div className="space-y-2">
                    <p className="text-sm font-medium">Rôle</p>
                    {roles.map((role) => (
                        <div key={role.id} className="flex items-center gap-2">
                            <input
                                type="radio"
                                id={`user-role-${role.id}`}
                                name="user-role"
                                className="size-4"
                                checked={roleId === role.id}
                                onChange={() => setRoleId(role.id)}
                            />
                            <Label htmlFor={`user-role-${role.id}`} className="font-normal">
                                {role.name}
                            </Label>
                        </div>
                    ))}
                    {errors.role_id && <p className="text-sm text-destructive">{errors.role_id}</p>}
                </div>

                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Annuler</Button>
                    </DialogClose>
                    <Button onClick={submit} disabled={processing || name.trim() === '' || email.trim() === '' || roleId === null}>
                        {editing ? 'Enregistrer' : 'Créer'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

function DeleteUserButton({ user, disabled }: { user: UserRow; disabled: boolean }) {
    const [confirming, setConfirming] = useState(false);

    const remove = () => {
        router.delete(`/users/${user.id}`, {
            preserveScroll: true,
            onFinish: () => setConfirming(false),
        });
    };

    return (
        <Dialog open={confirming} onOpenChange={setConfirming}>
            <Button
                variant="ghost"
                size="icon"
                aria-label={`Supprimer ${user.name}`}
                disabled={disabled}
                onClick={() => setConfirming(true)}
            >
                <Trash2 />
            </Button>
            <DialogContent>
                <DialogTitle>Supprimer « {user.name} » ?</DialogTitle>
                <DialogDescription>Cette action supprime définitivement le compte.</DialogDescription>
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
    );
}

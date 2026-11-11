# ERP Starter Kit

Point de départ pour une application ERP : gestion dynamique des rôles et des utilisateurs, permissions détaillées par action, interface React moderne.

## Stack

- **Backend** : Laravel 13, Spatie Permission (rôles et permissions), Spatie Activitylog, authentification à deux facteurs
- **Frontend** : React, TypeScript, Inertia.js, Tailwind CSS, TanStack Table, React Hook Form et Zod, Recharts, Sonner
- **Base de données** : MySQL (ou SQLite pour un essai rapide)

## Installation

```bash
composer run setup
```

Cette commande installe les dépendances PHP et JS, crée le fichier `.env`, génère la clé d'application et lance les migrations.

Puis lance les serveurs de développement :

```bash
composer run dev
```

## Compte administrateur

Le seeder crée un premier compte administrateur à partir des variables `ADMIN_EMAIL`, `ADMIN_NAME` et `ADMIN_PASSWORD` du fichier `.env`. Il reçoit le rôle **Administrateur**, qui conserve toutes les permissions et ne peut pas être supprimé.

Change le mot de passe par défaut avant toute mise en production.

## Permissions

Les permissions sont décrites dans `config/permission-tree.php`. Chaque ressource a une permission par action :

| Ressource   | Permissions                                              |
|-------------|----------------------------------------------------------|
| Utilisateurs | `users.view`, `users.create`, `users.update`, `users.delete` |
| Rôles       | `roles.view`, `roles.create`, `roles.update`, `roles.delete` |
| Tableau de bord | `dashboard.view`                                     |

Un utilisateur n'a qu'**un seul rôle** à la fois. Les routes sont protégées par le middleware `permission.auto`, qui relie chaque route à sa permission dans l'arbre.

Pour synchroniser les permissions en base après une modification de l'arbre :

```bash
php artisan db:seed --class=PermissionSeeder
```

## Tests et vérifications

```bash
composer run test       # tests PHP
npm run types:check     # vérification TypeScript
composer run lint:check # contrôle du style PHP et JS
```

## Commandes utiles

- `php artisan users:single-role --dry-run` : liste les utilisateurs qui ont plusieurs rôles, sans rien modifier
- `php artisan users:single-role` : ramène chaque utilisateur à un seul rôle

## Documentation

Le cahier des charges détaillé se trouve dans `DOCS/base_prompt.md`.

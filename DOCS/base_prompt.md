# MISSION : CRÉER UN STARTER KIT ERP PROFESSIONNEL ET RÉUTILISABLE

## 1. OBJECTIF DU PROJET

Tu es un architecte logiciel senior spécialisé dans Laravel, React, TypeScript, Inertia.js et la conception d'applications ERP professionnelles.

Ta mission est de concevoir et développer un Starter Kit complet, moderne, sécurisé, modulaire et réutilisable pour créer rapidement de nouvelles applications web de gestion (ERP, CRM, gestion commerciale, gestion financière, RH, stock, etc.).

Ce projet doit constituer une véritable fondation technique et fonctionnelle que je pourrai réutiliser pour chaque nouveau projet.

Le résultat doit être un projet fonctionnel, pas une simple démonstration visuelle.

Nom provisoire : ERP Starter Kit.

## 2. STACK TECHNIQUE OBLIGATOIRE

### Backend

- Laravel : dernière version stable compatible avec l'écosystème.
- PHP : version stable compatible.
- MySQL 8 / MariaDB.
- Laravel Eloquent ORM.
- Laravel Form Requests pour les validations.
- Laravel Policies pour les autorisations.
- Laravel Events et Listeners.
- Laravel Queues pour les traitements asynchrones.
- Laravel Notifications.
- Laravel Storage pour les fichiers.
- API REST extensible.
- Transactions SQL pour les opérations sensibles.

### Frontend

- React avec TypeScript.
- Inertia.js avec l'adaptateur React officiel.
- Tailwind CSS 4.
- shadcn/ui comme bibliothèque principale de composants.
- Lucide React pour les icônes.
- TanStack Table pour les tableaux avancés.
- React Hook Form pour les formulaires.
- Zod pour la validation frontend.
- Recharts pour les graphiques.
- Sonner pour les notifications Toast.
- date-fns pour les dates.
- Zustand uniquement lorsque la gestion d'état global devient nécessaire.

Privilégier les versions stables et compatibles entre elles. Vérifier les dépendances avant installation.

Ne pas utiliser Ant Design comme bibliothèque UI principale. Le Design System doit rester basé sur shadcn/ui et Tailwind CSS.

## 3. ARCHITECTURE DU PROJET

Adopter une architecture modulaire Feature-Based.

Organiser le frontend de façon à séparer :

- Les composants génériques.
- Les composants propres aux modules métier.
- Les pages Inertia.
- Les layouts.
- Les hooks.
- Les services frontend.
- Les schémas de validation.
- Les types TypeScript.
- Les constantes.

Structure indicative :

resources/js/
├── app.tsx
├── components/
│ ├── ui/
│ ├── shared/
│ ├── data-table/
│ ├── forms/
│ ├── navigation/
│ └── feedback/
├── layouts/
│ ├── app-layout.tsx
│ ├── auth-layout.tsx
│ └── guest-layout.tsx
├── pages/
│ ├── dashboard/
│ ├── profile/
│ ├── users/
│ ├── roles/
│ └── settings/
├── features/
│ ├── authentication/
│ ├── users/
│ ├── roles/
│ ├── permissions/
│ └── notifications/
├── hooks/
├── lib/
├── services/
├── types/
└── constants/

Côté Laravel, organiser proprement :

- Http/Controllers
- Http/Requests
- Http/Resources
- Models
- Actions
- Services
- Policies
- Enums
- Events
- Listeners
- Notifications
- Support

Éviter les contrôleurs surchargés et les duplications de logique métier.

Utiliser les Actions pour encapsuler les opérations métier réutilisables et les Services uniquement lorsqu'une véritable logique de coordination le justifie.

## 4. AUTHENTIFICATION COMPLÈTE

Implémenter un système d'authentification fonctionnel avec :

- Connexion.
- Déconnexion.
- Inscription activable ou désactivable.
- Mot de passe oublié.
- Réinitialisation du mot de passe.
- Vérification de l'adresse e-mail.
- Confirmation du mot de passe pour les opérations sensibles.
- Gestion des sessions.
- Protection CSRF.
- Limitation des tentatives de connexion.
- Gestion des comptes actifs et désactivés.
- Page de profil utilisateur.
- Modification des informations personnelles.
- Modification du mot de passe.
- Photo de profil.
- Gestion des sessions actives si possible.

Prévoir également une authentification à deux facteurs (2FA), avec activation, désactivation et codes de récupération.

Utiliser les mécanismes natifs de Laravel ou les fonctionnalités officielles de son starter kit lorsque cela est pertinent. Ne pas réinventer la gestion des mots de passe et des sessions.

## 5. GESTION DES UTILISATEURS, RÔLES ET PERMISSIONS

Utiliser Spatie Laravel Permission.

Créer un système RBAC (Role-Based Access Control) complet.

Fonctionnalités :

- CRUD utilisateurs.
- CRUD rôles.
- CRUD permissions.
- Attribution de plusieurs rôles à un utilisateur.
- Attribution directe de permissions.
- Gestion des permissions par module.
- Activation et désactivation des utilisateurs.
- Filtrage et recherche.
- Pagination serveur.
- Historique des actions sensibles.

Prévoir des permissions granulaires :

Exemples :

- users.view
- users.create
- users.update
- users.delete
- users.export
- roles.view
- roles.create
- roles.update
- roles.delete
- settings.manage

Créer un middleware et des Policies pour protéger les routes et les opérations.

Les permissions doivent être contrôlées côté serveur. Le frontend ne doit jamais constituer une barrière de sécurité.

Créer un rôle Super Admin avec accès complet, ainsi qu'un rôle Administrateur configurable.

Prévoir des commandes et seeders pour initialiser les rôles et permissions.

## 6. DESIGN SYSTEM ERP PROFESSIONNEL

Créer une interface moderne, élégante, cohérente et adaptée aux logiciels ERP professionnels.

Inspiration visuelle :

- Linear.
- Stripe Dashboard.
- Vercel Dashboard.
- Interfaces SaaS Enterprise modernes.

### Direction artistique

- Interface claire et minimaliste.
- Sidebar professionnelle.
- Header avec recherche et actions rapides.
- Typographie lisible.
- Espacements cohérents.
- Bordures discrètes.
- Cartes élégantes.
- Icônes Lucide.
- Couleurs sobres et professionnelles.
- Animations discrètes.
- Responsive Desktop, Tablet et Mobile.

### Palette proposée

- Primary : Indigo / Blue.
- Background : Slate 50.
- Surface : White.
- Sidebar : Slate 900.
- Text : Slate 900.
- Muted : Slate 500.
- Success : Emerald.
- Warning : Amber.
- Danger : Red.
- Info : Blue.

Centraliser les couleurs et les variables CSS pour permettre la personnalisation de chaque nouveau projet.

### Layout principal

Créer un layout ERP comprenant :

1. Sidebar :
    - Logo et nom de l'application.
    - Navigation hiérarchique.
    - Groupes de menus.
    - Sous-menus.
    - Menus actifs.
    - Menus affichés selon les permissions.
    - Sidebar rétractable.
    - Version mobile avec navigation adaptée.

2. Header :
    - Breadcrumb dynamique.
    - Barre de recherche.
    - Command Menu.
    - Centre de notifications.
    - Sélecteur de thème.
    - Menu utilisateur.
    - Accès rapide aux paramètres.

3. Zone principale :
    - Page Header.
    - Titre.
    - Description.
    - Actions principales.
    - Contenu dynamique.
    - Gestion des états de chargement.

### Thèmes

Implémenter :

- Mode clair.
- Mode sombre.
- Mode système.
- Persistance de la préférence utilisateur.

## 7. COMPOSANTS GÉNÉRIQUES RÉUTILISABLES

Créer un ensemble de composants standardisés.

### DataTable

Basé sur TanStack Table.

Fonctionnalités :

- Pagination serveur.
- Recherche globale.
- Tri multi-colonnes.
- Filtres par colonne.
- Sélection multiple.
- Actions par ligne.
- Colonnes personnalisables.
- Masquage des colonnes.
- États vides.
- États de chargement.
- Export CSV.
- Sélection du nombre de lignes.
- Filtres avancés.
- Synchronisation des filtres avec les paramètres URL lorsque pertinent.

Le composant doit être générique et réutilisable dans tous les futurs modules.

### Formulaires

Créer :

- FormField.
- FormInput.
- FormSelect.
- FormTextarea.
- FormDatePicker.
- FormCheckbox.
- FormSwitch.
- FormCombobox.
- FormFileUpload.

Utiliser React Hook Form et Zod.

Gérer :

- Validation.
- Messages d'erreur.
- Champs obligatoires.
- États de chargement.
- Soumission.
- Réinitialisation.
- Valeurs par défaut.

### Autres composants

Créer :

- PageHeader.
- StatCard.
- StatusBadge.
- ConfirmDialog.
- FormDialog.
- DeleteDialog.
- FilterBar.
- SearchInput.
- EmptyState.
- LoadingState.
- ErrorState.
- Breadcrumbs.
- CommandMenu.
- NotificationCenter.
- UserAvatar.
- ThemeToggle.
- DateRangePicker.

Chaque composant doit avoir des propriétés TypeScript propres et une documentation d'utilisation.

## 8. DASHBOARD PROFESSIONNEL

Créer un dashboard d'administration comprenant :

- Nombre total d'utilisateurs.
- Utilisateurs actifs.
- Nouveaux utilisateurs.
- Activités récentes.
- Graphique d'évolution.
- Répartition des utilisateurs.
- Dernières opérations.
- Notifications importantes.
- Raccourcis vers les modules.

Utiliser Recharts.

Les indicateurs doivent être alimentés par des données réelles provenant de Laravel. Les données fictives ne sont acceptées que dans les environnements de démonstration.

## 9. NOTIFICATIONS ET JOURNAL D'ACTIVITÉ

Implémenter :

### Notifications

- Notifications en base de données.
- Centre de notifications.
- Compteur de notifications non lues.
- Marquer comme lue.
- Marquer toutes comme lues.
- Notifications de succès, avertissement et erreur.
- Prévoir une architecture extensible vers les notifications temps réel.

### Journal d'activité

- Utiliser Spatie Activitylog.
- Enregistrer les opérations sensibles.
- Identifier l'utilisateur responsable.
- Enregistrer la date et l'heure.
- Conserver les changements importants.
- Prévoir une page de consultation avec filtres.

Ne jamais enregistrer les mots de passe, tokens ou autres secrets dans les journaux.

## 10. PARAMÈTRES GÉNÉRAUX

Créer une section Paramètres comprenant :

- Informations de l'application.
- Nom de l'application.
- Logo.
- Favicon.
- Coordonnées.
- Langue.
- Fuseau horaire.
- Format de date.
- Format numérique.
- Devise par défaut.
- Configuration de l'apparence.
- Paramètres de sécurité.

Les paramètres doivent être stockés en base de données et gérés par une couche dédiée.

Prévoir le support multilingue, avec français et anglais comme langues initiales.

## 11. SÉCURITÉ ET QUALITÉ

Respecter les bonnes pratiques Laravel et React :

- Protection contre les injections SQL.
- Protection XSS.
- Validation serveur systématique.
- Autorisation côté serveur.
- Gestion sécurisée des fichiers.
- Vérification des permissions sur les téléchargements.
- Protection des routes.
- Transactions SQL.
- Gestion des erreurs.
- Logs structurés.
- Configuration des environnements.
- Secrets exclusivement dans les variables d'environnement.

Ne jamais faire confiance aux validations frontend.

Éviter les requêtes N+1 avec Eloquent.

Utiliser des index SQL adaptés.

Ne pas exposer de données sensibles dans les props Inertia.

## 12. TESTS

Prévoir :

### Backend

- Tests PHPUnit ou Pest.
- Tests des routes.
- Tests d'authentification.
- Tests des rôles et permissions.
- Tests CRUD.
- Tests de validation.
- Tests des Policies.

### Frontend

- Vitest.
- React Testing Library.

Créer des tests pour les composants critiques, les formulaires et les interactions principales.

## 13. PERFORMANCE

Optimiser :

- Chargement des pages.
- Chargement différé des données non essentielles.
- Pagination serveur.
- Requêtes SQL.
- Chargement des composants.
- Images.
- Assets Vite.
- Mise en cache lorsque nécessaire.

Éviter de transférer inutilement de grandes quantités de données avec Inertia.

## 14. DOCUMENTATION ET RÉUTILISABILITÉ

Créer une documentation complète :

- README.md.
- INSTALLATION.md.
- ARCHITECTURE.md.
- AUTHENTICATION.md.
- PERMISSIONS.md.
- COMPONENTS.md.
- DEPLOYMENT.md.
- DEVELOPMENT.md.

Documenter :

- Installation.
- Configuration.
- Création du premier administrateur.
- Création des rôles.
- Ajout d'un nouveau module.
- Création d'une nouvelle page.
- Création d'une nouvelle permission.
- Utilisation des composants génériques.
- Personnalisation du thème.

Prévoir un système de configuration permettant de modifier facilement :

- Nom du projet.
- Logo.
- Couleurs.
- Modules activés.
- Informations de l'application.

## 15. CONTRAINTES DE DÉVELOPPEMENT

1. Développer une véritable application fonctionnelle.
2. Ne pas se limiter à générer des maquettes.
3. Ne pas laisser de boutons sans action.
4. Ne pas utiliser de données statiques pour simuler les fonctionnalités en production.
5. Respecter les conventions Laravel et React.
6. Utiliser TypeScript strict.
7. Éviter les composants monolithiques.
8. Centraliser les composants réutilisables.
9. Éviter les duplications.
10. Maintenir une cohérence graphique sur toutes les pages.
11. Utiliser les dernières versions stables compatibles.
12. Ne pas installer de dépendances inutiles.
13. Respecter les performances et la sécurité.

## 16. MÉTHODOLOGIE DE RÉALISATION

Travailler progressivement.

### Phase 1 : Analyse et initialisation

- Vérifier l'environnement.
- Vérifier les versions disponibles.
- Initialiser Laravel.
- Configurer Inertia et React.
- Configurer TypeScript et Tailwind.
- Installer shadcn/ui.
- Installer les dépendances.

### Phase 2 : Fondations

- Organiser l'architecture.
- Créer le Design System.
- Créer les layouts.
- Créer la navigation.
- Créer les composants génériques.

### Phase 3 : Authentification

- Implémenter les fonctionnalités d'authentification.
- Créer les interfaces.
- Tester les flux.

### Phase 4 : Administration

- Utilisateurs.
- Rôles.
- Permissions.
- Journal d'activité.
- Notifications.
- Paramètres.

### Phase 5 : Dashboard

- Indicateurs réels.
- Graphiques.
- Activités récentes.

### Phase 6 : Qualité

- Tests.
- Sécurité.
- Optimisation.
- Documentation.

## 17. CRITÈRES DE VALIDATION FINALE

Le projet ne sera considéré comme terminé que lorsque :

- L'installation fonctionne sur un environnement propre.
- L'authentification est opérationnelle.
- La réinitialisation du mot de passe fonctionne.
- Les permissions sont réellement appliquées.
- Les utilisateurs peuvent être administrés.
- Les rôles et permissions sont fonctionnels.
- Le dashboard utilise les données du backend.
- Les composants DataTable et Form sont réutilisables.
- Les thèmes clair et sombre fonctionnent.
- Le responsive est opérationnel.
- Les tests critiques passent.
- La documentation permet à un développeur de démarrer un nouveau projet.

À la fin, fournir :

1. L'architecture complète.
2. Les dépendances installées et leurs versions.
3. Les commandes d'installation.
4. Les comptes ou instructions de création du premier administrateur.
5. Les résultats des tests.
6. Les éventuelles limitations restantes.

IMPORTANT : ne pas prétendre qu'une fonctionnalité est terminée si elle n'a pas été réellement implémentée et testée.

Commence par analyser l'environnement et proposer un plan technique. Ensuite, réalise effectivement le projet étape par étape jusqu'à obtenir un Starter Kit ERP opérationnel et documenté.

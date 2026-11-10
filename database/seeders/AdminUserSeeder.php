<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Crée le premier administrateur de l'application. Sans lui, personne ne peut
 * gérer les rôles.
 *
 * Idempotent : relançable sans doublon. Sur un compte existant, seuls le nom,
 * le mot de passe et le rôle sont réaffirmés ; l'e-mail est la clé.
 *
 * Configurable via ADMIN_EMAIL, ADMIN_NAME et ADMIN_PASSWORD. Le mot de passe
 * par défaut est « password » : à changer dès que l'installation n'est plus
 * locale.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) env('ADMIN_EMAIL', 'nysainana@dna.mg'));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->command?->warn('Aucun administrateur créé : ADMIN_EMAIL est invalide.');

            return;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => (string) env('ADMIN_NAME', 'Ny Sainana'),
                'password' => Hash::make((string) env('ADMIN_PASSWORD', 'password')),
                'email_verified_at' => Carbon::now(),
            ]
        );

        $user->syncRoles([PermissionSeeder::ADMIN_ROLE]);

        $this->command?->info("Administrateur en place : {$user->email}.");
    }
}

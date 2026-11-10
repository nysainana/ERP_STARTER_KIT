<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:single-role {--dry-run : Affiche les changements sans les appliquer}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $changed = 0;

    User::query()->with('roles')->chunkById(200, function ($users) use ($dryRun, &$changed) {
        foreach ($users as $user) {
            $roles = $user->roles->sortBy('id')->values();

            if ($roles->count() <= 1) {
                continue;
            }

            $keep = $roles->first();
            $this->line("{$user->email} : garde « {$keep->name} », retire ".$roles->slice(1)->pluck('name')->implode(', '));
            $changed++;

            if (! $dryRun) {
                DB::transaction(fn () => $user->syncRoles([$keep]));
            }
        }
    });

    $this->info($dryRun
        ? "{$changed} utilisateur(s) à corriger (simulation, rien n'a été modifié)."
        : "{$changed} utilisateur(s) ramené(s) à un seul rôle.");
})->purpose('Ramène chaque utilisateur à un seul rôle (le premier par identifiant)');

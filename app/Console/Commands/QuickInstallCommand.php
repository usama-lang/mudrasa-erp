<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\InstallationService;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#[Signature('app:install {--force : Re-run even if the application is already installed}')]
#[Description('Run the full installation (migrate, seed, enable modules, mark installed) non-interactively so the web install wizard never shows.')]
class QuickInstallCommand extends Command
{
    public function __construct(private readonly InstallationService $installationService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (InstallationService::isLikelyInstalled() && ! $this->option('force')) {
            $this->info('Application is already installed. Use --force to re-run.');

            return self::SUCCESS;
        }

        if (! $this->installationService->hasValidAppKey()) {
            $this->info('Generating APP_KEY...');
            $this->installationService->generateAppKey();
        }

        $this->info('Running core migrations...');
        $result = $this->installationService->runMigrations();

        if (! $result['success']) {
            $this->error($result['message']);

            return self::FAILURE;
        }

        $this->info('Ensuring roles & permissions...');
        $this->installationService->ensureRolesAndPermissions();

        if (! User::query()->exists()) {
            $this->info('Seeding default users (superadmin/admin/subscriber)...');
            Artisan::call('db:seed', ['--class' => UserSeeder::class, '--force' => true]);
        }

        $this->info('Running module migrations...');
        Artisan::call('module:migrate', ['--all' => true, '--force' => true]);

        $this->info('Marking installation as complete...');
        $this->installationService->completeInstallation();

        $this->info('Done. The app will go straight to the login screen from now on.');

        return self::SUCCESS;
    }
}

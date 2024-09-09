<?php

namespace Lacodix\LaravelPlans;

use Lacodix\LaravelPlans\Commands\RenewSubscriptions;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPlansServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-plans')
            ->hasConfigFile()
            ->hasMigrations([
                'create_laravel_plans_tables',
            ])
            ->hasCommand(RenewSubscriptions::class)
            ->hasInstallCommand(static function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToStarRepoOnGitHub('lacodix/laravel-plans');
            });
    }
}

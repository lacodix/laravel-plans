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
                '2024_07_04_000001_create_plans_table',
                '2024_07_04_000002_create_features_table',
                '2024_07_04_000003_create_feature_plan_table',
                '2024_07_04_000004_create_subscriptions_table',
                '2024_07_04_000005_create_feature_usages_table',
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

<?php

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Lacodix\LaravelPlans\LaravelPlansServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use LazilyRefreshDatabase;
    use InteractsWithViews;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        foreach (scandir(__DIR__ .'/../database/migrations') as $filename) {
            if (Str::endsWith($filename, '.php.stub')) {
                $migration = include __DIR__ .'/../database/migrations/' . $filename;
                $migration->up();
            }
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Factory::guessFactoryNamesUsing(
            function (string $modelName) {
                if (class_exists('Lacodix\\LaravelPlans\\Database\\Factories\\'.Str::afterLast($modelName, '\\').'Factory')) {
                    return 'Lacodix\\LaravelPlans\\Database\\Factories\\'.Str::afterLast($modelName, '\\').'Factory';
                }

                return 'Tests\\Database\\Factories\\'.Str::afterLast($modelName, '\\').'Factory';
            }
        );

        Factory::guessModelNamesUsing(
            function (Factory $factory) {
                $modelBaseName = Str::replaceLast(
                    'Factory',
                    '',
                    Str::afterLast($factory::class, '\\')
                );

                if (class_exists('Lacodix\\LaravelPlans\\Models\\' . $modelBaseName)) {
                    return 'Lacodix\\LaravelPlans\\Models\\' . $modelBaseName;
                }
                return 'Tests\\Models\\'.$modelBaseName;
            }
        );
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelPlansServiceProvider::class,
        ];
    }
}

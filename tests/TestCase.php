<?php

namespace KaanTanis\FilamentModelTranslatable\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use KaanTanis\FilamentModelTranslatable\FilamentModelTranslatableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'KaanTanis\\FilamentModelTranslatable\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentModelTranslatableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_filament-model-translatable_table.php.stub';
        $migration->up();
        */
    }
}

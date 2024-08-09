<?php

namespace KaanTanis\FilamentModelTranslatable;

use Filament\Forms\Components\Field;
use Spatie\LaravelPackageTools\Package;
use Filament\Forms\Components\Actions\Action;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use KaanTanis\FilamentModelTranslatable\Facades\FilamentModelTranslatable;
use KaanTanis\FilamentModelTranslatable\Commands\FilamentModelTranslatableCommand;

class FilamentModelTranslatableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-model-translatable')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_filament_model_translatable_table')
            ->hasCommand(FilamentModelTranslatableCommand::class);
    }

    public function boot()
    {
        parent::boot();

        Field::macro('translatable', function() {
            $component = clone $this;

            $locales = config('filament-model-translatable.supported_locales', ['en']);

            $actions = collect($locales)->map(function ($locale) use ($component) {
                return Action::make($locale)
                    ->label(str($locale)->upper())
                    ->form([
                        $component->formatStateUsing(function ($model, $record, $component) use ($locale) {
                            $key = $component->getName();
                            $translation = FilamentModelTranslatable::getTranslate($model, $record->id, $key, $locale);

                            return $translation ?? $record->{$key};
                        })
                    ])->action(function ($model, $record, $data) use ($locale) {
                        $key = key($data);
                        $value = $data[$key];

                        FilamentModelTranslatable::setTranslate($model, $record->id, $key, $value, $locale);
                    });
            })->toArray();

            return $this->hintAction(fn () => $actions);
        });
    }

}

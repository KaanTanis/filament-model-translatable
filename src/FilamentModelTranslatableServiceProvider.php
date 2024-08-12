<?php

namespace KaanTanis\FilamentModelTranslatable;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use KaanTanis\FilamentModelTranslatable\Commands\FilamentModelTranslatableCommand;
use KaanTanis\FilamentModelTranslatable\Facades\FilamentModelTranslatable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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

        // todo: fix repeater
        // todo: check fileupload, select and other fields
        // todo: remove unnecessary translations

        Field::macro('translatable', function ($translatable = true) {
            if (! $translatable) {
                return $this;
            }

            $component = clone $this;

            $locales = config('filament-model-translatable.supported_locales', ['en']);

            $actions = collect($locales)->map(function ($locale) use ($component) {
                $localizedComponent = clone $component;

                $localizedComponent->formatStateUsing(function ($model, $record, $component) use ($locale) {
                    $key = $component->getName();
                    $translation = FilamentModelTranslatable::getTranslate($model, $record->id, $key, $locale);

                    return $translation ?? $record->{$key};
                });

                return Action::make($locale)
                    ->label(str($locale)->upper())
                    ->form([$localizedComponent])
                    ->hidden(fn ($record) => ! $record)
                    ->action(function ($model, $record, $data) use ($locale) {
                        $key = key($data);
                        $value = $data[$key];

                        FilamentModelTranslatable::setTranslate($model, $record->id, $key, $value, $locale);
                    });
            })->toArray();

            return $this->hintAction(fn () => $actions);
        });

    }
}

<?php

namespace KaanTanis\FilamentModelTranslatable;

use Illuminate\Support\Carbon;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Spatie\LaravelPackageTools\Package;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
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

        // todo: check fileupload, select and other fields
        Field::macro('translatable', function () {
            $component = clone $this;

            $locales = config('filament-model-translatable.supported_locales', ['en']);

            $actions = collect($locales)->map(function ($locale) use ($component) {
                $localizedComponent = clone $component;

                $localizedComponent->formatStateUsing(function ($model, $record, $component) use ($locale) {
                    $key = $component->getName();
                    $translation = FilamentModelTranslatable::getTranslate($model, $record->id, $key, $locale);

                    $value = $translation ?? $record->getAttribute($key) ?? null;

                    if ($component instanceof FileUpload) {
                        $value = $component->isMultiple() ? $value : [$value];
                    }

                    if ($component instanceof Checkbox || $component instanceof Toggle) {
                        $value = $value ? true : false;
                    }

                    return $value;
                });

                return Action::make($locale)
                    ->slideOver()
                    ->color(function ($model, $record, $component) use ($locale) {
                        $key = $component->getName();
                        $translation = FilamentModelTranslatable::getTranslate($model, $record->id, $key, $locale);

                        return is_null($translation) ? 'danger' : 'success';
                    })
                    ->label(str($locale)->upper())
                    ->form([$localizedComponent])
                    ->hidden(function ($record, $component) {
                        if (is_null($record)) {
                            return true;
                        }

                        $parentComponent = $component->getContainer()->getParentComponent();

                        if ($parentComponent instanceof Repeater) {
                            return true;
                        }
                    })
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

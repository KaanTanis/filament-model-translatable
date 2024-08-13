<?php

namespace KaanTanis\FilamentModelTranslatable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;
use KaanTanis\FilamentModelTranslatable\Models\ModelTranslatable as Model;

trait ModelTranslatable
{
    /**
     * Get the translations for the model.
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Model::class, 'model');
    }

    /**
     * Get the value of a specific key.
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->translatable)) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Get the translation for a specific key.
     */
    public function getTranslation(string $key, ?string $locale = null)
    {
        $locale ??= app()->getLocale();
        $modelClass = get_class($this);
        $id = $this->id;

        // Use caching to retrieve the translation
        return Cache::remember(
            $this->getCacheKey($modelClass, $id, $key, $locale),
            now()->addMinutes(config('filament-model-translatable.cache_time', 10)),
            function () use ($key, $locale) {
                $translate = $this->translations()
                    ->where('key', $key)
                    ->where('locale', $locale)
                    ->first();

                return $translate ? $translate->value : $this->attributes[$key] ?? null;
            }
        );
    }

    /**
     * Get the cache key for a translation.
     */
    protected function getCacheKey(string $modelClass, int $id, string $key, string $locale): string
    {
        return "translation.{$modelClass}.{$id}.{$key}.{$locale}";
    }

    /**
     * Clear the cache for a specific translation.
     */
    protected function clearCache(string $modelClass, int $id, string $key, string $locale): void
    {
        Cache::forget($this->getCacheKey($modelClass, $id, $key, $locale));
    }

    /**
     * Clear all translations cache.
     */
    public static function bootModelTranslatable()
    {
        static::saved(function ($model) {
            $modelClass = get_class($model);
            $id = $model->id;

            foreach ($model->translations as $translation) {
                $model->clearCache($modelClass, $id, $translation->key, $translation->locale);
            }
        });

        static::deleted(function ($model) {
            $model->translations()->delete();

            // Clear cache for all translations after deletion
            $modelClass = get_class($model);
            $id = $model->id;

            foreach ($model->translations as $translation) {
                $model->clearCache($modelClass, $id, $translation->key, $translation->locale);
            }
        });
    }
}

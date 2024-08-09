<?php

namespace KaanTanis\FilamentModelTranslatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use KaanTanis\FilamentModelTranslatable\Models\ModelTranslatable;

class FilamentModelTranslatable
{
    /**
     * Set the translation for a model.
     */
    public function setTranslate(string $modelClass, int $id, string $key, string $value, string $locale): bool
    {
        try {
            ModelTranslatable::updateOrCreate(
                [
                    'model_id' => $id,
                    'model_type' => $modelClass,
                    'key' => $key,
                    'locale' => $locale,
                ],
                [
                    'value' => $value,
                ]
            );

            $this->clearCache($modelClass, $id, $key, $locale);

            return true;
        } catch (\Exception $e) {
            Log::error("Error setting translation: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Get the translation for a model.
     */
    public function getTranslate(string $modelClass, int $id, string $key, string $locale): ?string
    {
        return Cache::remember(
            $this->getCacheKey($modelClass, $id, $key, $locale),
            now()->addMinutes(config('filament-model-translatable.cache_time', 10)),
            function () use ($modelClass, $id, $key, $locale) {
                $translation = ModelTranslatable::where('model_type', $modelClass)
                    ->where('model_id', $id)
                    ->where('key', $key)
                    ->where('locale', $locale)
                    ->first();

                return $translation?->value;
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
}

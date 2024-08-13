<?php

namespace KaanTanis\FilamentModelTranslatable;

use KaanTanis\FilamentModelTranslatable\Models\ModelTranslatable;

class FilamentModelTranslatable
{
    /**
     * Set the translation for a model.
     */
    public function setTranslate(string $modelClass, int $id, string $key, mixed $value, string $locale): void
    {
        try {
            $value_type = match (gettype($value)) {
                'array' => 'array',
                'string' => 'string',
                'integer' => 'integer',
                'double' => 'float',
                'boolean' => 'boolean',
                default => 'string',
            };

            ModelTranslatable::updateOrCreate(
                [
                    'model_id' => $id,
                    'model_type' => $modelClass,
                    'key' => $key,
                    'locale' => $locale,
                ],
                [
                    'value' => $value,
                    'value_type' => $value_type,
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception("Error setting translation: {$e->getMessage()}");
        }
    }

    /**
     * Get the translation for a model.
     */
    public function getTranslate(string $modelClass, int $id, string $key, string $locale): mixed
    {
        $translation = ModelTranslatable::where('model_type', $modelClass)
            ->where('model_id', $id)
            ->where('key', $key)
            ->where('locale', $locale)
            ->first();

        return $translation?->value;
    }
}

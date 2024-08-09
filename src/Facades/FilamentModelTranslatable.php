<?php

namespace KaanTanis\FilamentModelTranslatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KaanTanis\FilamentModelTranslatable\FilamentModelTranslatable
 */
class FilamentModelTranslatable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \KaanTanis\FilamentModelTranslatable\FilamentModelTranslatable::class;
    }
}

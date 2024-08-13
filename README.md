# Filament Model Translatable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kaantanis/filament-model-translatable.svg?style=flat-square)](https://packagist.org/packages/kaantanis/filament-model-translatable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kaantanis/filament-model-translatable/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kaantanis/filament-model-translatable/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kaantanis/filament-model-translatable/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kaantanis/filament-model-translatable/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kaantanis/filament-model-translatable.svg?style=flat-square)](https://packagist.org/packages/kaantanis/filament-model-translatable)

Filament model translatable is a package that provides a trait to make your models translatable in Laravel Filament. The data is stored in a new table with the model's id, the field name, the language, and the value.

## Installation

You can install the package via composer:

```bash
composer require kaantanis/filament-model-translatable
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-model-translatable-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-model-translatable-config"
```

This is the contents of the published config file:

```php
<?php
return [
    // except default locale on your app.php
    'supported_locales' => [
        'tr',
        'de',
    ],

    'cache_time' => 10, // in minutes
];
```

## Usage

```php
// model
use KaanTanis\FilamentModelTranslatable\Traits\ModelTranslatable;

class Post extends Model
{
    use ModelTranslatable;

    protected $translatable = [
        'title',
        'body',
    ];
}
```
```php
// Resource
TextInput::make('title')
    ->translatable() // It works magically via macro
```
```php
// Get the value
$model->title // It will return the value of the app()->getLocale()
$model->getTranslation('title', 'tr') // It will return the value of the target locale

// if the given locale does not exist from database, it will return the title of the model itself
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [KaanTanis](https://github.com/KaanTanis)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

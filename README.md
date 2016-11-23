# Laravel 5 Profanity Filter
Filter profanity, or other words, out of a string using Laravels [localization](https://laravel.com/docs/5.3/localization) feature.

# Installation
```
composer require askedio/laravel5-profanity-filter
```
## Register in `config/app.php`
Register the service providers to enable the package:
```
Askedio\Laravel5ProfanityFilter\Providers\ProfanityFilterServiceProvider::class,
```

Autoload it:
```
composer dumpautoload
```

# Configure
```
php artisan vendor:publish
```

You can edit the default list of words to filter along with the settings in `config/profanity.php`.

You can create your own list of words, per language, in `resources/lang/[language]/profanity.php`.

# Usage
```php
$string = app('profanityFilter')->filter('something with a bad word');
```


# Credits
This package is based on [banbuilder](https://github.com/snipe/banbuilder).
[![Build Status](https://travis-ci.org/Askedio/laravel-profanity-filter.svg?branch=master)](https://travis-ci.org/Askedio/laravel-profanity-filter)
[![StyleCI](https://styleci.io/repos/74531615/shield?branch=master)](https://styleci.io/repos/74531615)

# Laravel Profanity Filter
Filter profanity, or other bad words, out of a string using Laravels [localization](https://laravel.com/docs/5.6/localization) feature or with any PHP application and some custom coding.

# Installation
```
composer require askedio/laravel5-profanity-filter
```

## Register in config/app.php.
Register the service providers in Laravel 5.4 or lower to enable the package:
```
Askedio\Laravel5ProfanityFilter\Providers\ProfanityFilterServiceProvider::class,
```

## Configure
```
php artisan vendor:publish
```

You can edit the default list of words to filter along with the settings in `config/profanity.php`.

`replaceWith` can also be a string of chars to be randomly chosen to replace with, like `'&%^@#'`.

You can create your own list of words, per language, in `resources/lang/[language]/profanity.php`.


# Usage
```php
$string = app('profanityFilter')->filter('something with a bad word');
```
The `$string` will contain the filtered result.

You can also define things inline
```php
$string = app('profanityFilter')->replaceWith('#')->replaceFullWords(false)->filter('something with a bad word'));
```

You can also use the `profanity` filter with Laravels [Validation](https://laravel.com/docs/5.6/validation) feature:

```php
$request->validate([
    'title' => 'required|profanity|unique:posts|max:255',
]);
```

# Options
* `filter($string = string, $details = boolean)` pass a string to be filtered.

  * Enable details to have an array of results returned:
    ```php
    [
      "orig" => "",
      "clean" => "",
      "hasMatch" => boolean,
      "matched" => []
    ]
    ```
* `reset()` reset `replaceWith` and `replaceFullWords` to defaults.
* `replaceWith(string)` change the chars used to replace filtered strings.
* `replaceFullWords(boolean)` enable to replace full words, disable to replace partial.


# Profanity Filter with PHP
You can also use this package without Laravel.

```php
use Askedio\Laravel5ProfanityFilter\ProfanityFilter;

$config = []; // Data from `resources/config/profanity.php`
$badWordsArray = []; // Data from `resources/lang/[lang]/profanity.php`

$profanityFilter =  new ProfanityFilter($config, $badWordsArray);
$string = $profanityFilter->filter('something with a bad word');
```



# Credits
This package is based on [banbuilder](https://github.com/snipe/banbuilder).

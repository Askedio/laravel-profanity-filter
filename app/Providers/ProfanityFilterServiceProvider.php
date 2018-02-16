<?php

namespace Askedio\Laravel5ProfanityFilter\Providers;

use Askedio\Laravel5ProfanityFilter\ProfanityFilter;
use Illuminate\Support\ServiceProvider;

class ProfanityFilterServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../resources/config/profanity.php'), 'profanity');

        $this->app->singleton('profanityFilter', function () {
            $translations = trans('profanity::profanity');

            return new ProfanityFilter(config('profanity'), is_array($translations) ? $translations : []);
        });
    }

    /**
     * Register routes, translations, views and publishers.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(realpath(__DIR__.'/../../resources/lang'), 'profanity');

        $this->publishes([
          realpath(__DIR__.'/../../resources/config/profanity.php') => config_path('profanity.php'),
          realpath(__DIR__.'/../../resources/lang')                 => resource_path('lang/vendor/profanity'),
        ], 'config');

        app('validator')->extend('profanity', function ($attribute, $value, $parameters, $validator) {
            $replace = [
              $attribute => app('profanityFilter')->filter($value),
            ];

            request()->replace($replace);

            $validator->setData($replace);

            return true;
        });
    }
}

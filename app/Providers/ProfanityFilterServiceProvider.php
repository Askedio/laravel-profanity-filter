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
            return new ProfanityFilter();
        });
    }

    /**
     * Register routes, translations, views and publishers.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(realpath(__DIR__.'/../../resources/lang'), 'Laravel5ProfanityFilter');

        $this->publishes([realpath(__DIR__.'/../../resources/config/profanity.php') => config_path('profanity.php')], 'config');
    }
}

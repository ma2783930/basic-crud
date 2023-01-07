<?php

namespace BasicCrud\Providers;

use BasicCrud\Helpers\BasicCrudRouteHelper;
use Illuminate\Support\ServiceProvider;

class BasicCrudServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('basicCrudRouteHelper', function () {
            return new BasicCrudRouteHelper;
        });
    }

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '../../lang', 'basic-crud');
    }
}

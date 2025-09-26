<?php

namespace UserDiscounts;

use Illuminate\Support\ServiceProvider;

class UserDiscountsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // config merge
        $this->mergeConfigFrom(__DIR__.'/../config/discounts.php','discounts');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->publishes([__DIR__.'/../config/discounts.php'=>config_path('discounts.php')]);
    }
}

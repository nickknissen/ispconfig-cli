<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\ISPConfig;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ISPConfig::class, function ($app) {
            return new ISPConfig(
                config('ispconfig.api_url'),
                config('ispconfig.api_username'),
                config('ispconfig.api_password')
            );
        });
    }
}

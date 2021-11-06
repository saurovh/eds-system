<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Saurovh\EdsPhpSdk\Api;
use Illuminate\Log\Logger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Api::class, function () {
            $api = Api::init(env('EDS_API_KEY'));
            $api->setLogger($this->app->get(Logger::class));

            return $api;
        });

        /**
         * Initiating also, so that from application label no need to initiate
         */
        $this->app->get(Api::class);
    }

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {

    }
}

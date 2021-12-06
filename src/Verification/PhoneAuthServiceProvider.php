<?php

namespace PhoneAuth\Support\Verification;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PhoneAuthServiceProvider extends ServiceProvider implements DeferrableProvider
{

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerVerificationBroker();
    }

    /**
     * Register the verification broker instance.
     *
     * @return void
     */
    protected function registerVerificationBroker()
    {
        $this->app->singleton('phoneauth.verification', function ($app) {
            return new VerificationBrokerManager($app);
        });

        $this->app->bind('phoneauth.verification.broker', function ($app) {
            return $app->make('phoneauth.verification')->broker();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'phoneauth.verification' ,
            'phoneauth.verification.broker'
        ];
    }
}

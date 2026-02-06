<?php

namespace App\Providers;

use Resend;
use App\Services\JWTServices;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(JWTServices::class, JWTServices::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Mail::extend('resend', function () {
            return new \Illuminate\Mail\Transport\ResendTransport(
                Resend::client(config('services.resend.key'))
            );
        });
    }
}

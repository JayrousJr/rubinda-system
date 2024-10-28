<?php

namespace App\Providers;

use App\Models\FeePayment;
use App\Observers\FeePaymentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FeePayment::observe(FeePaymentObserver::class);
    }
}
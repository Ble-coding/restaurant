<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Coupon;
use App\Observers\CouponObserver;
use Carbon\Carbon;

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
        // Enregistrer l'Observer pour le modèle Coupon
        Coupon::observe(CouponObserver::class);
        Carbon::setLocale('fr');
    }
}

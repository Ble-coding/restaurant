<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Coupon;
use App\Observers\CouponObserver;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

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

        // Gate::define('view-orders', function ($user) {
        //     return $user->hasPermissionTo('view-orders');
        // });



        // Enregistrer l'Observer pour le modèle Coupon
        Coupon::observe(CouponObserver::class);
        Carbon::setLocale('fr');
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Stripe;
use App\Models\PaymentGateway;

class StripeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Stripe::class, function ($app) {
            $locale = app()->getLocale(); // Récupérer la langue actuelle (fr ou en)

            $paymentGateway = PaymentGateway::whereHas('payment', function ($query) use ($locale) {
                $query->whereRaw("JSON_EXTRACT(name, '$.$locale') = ?", ['Stripe']);
            })->first();

            if (!$paymentGateway) {
                throw new \Exception("Passerelle Stripe non configurée.");
            }

            return new Stripe($paymentGateway->api_key, $paymentGateway->secret_key);
        });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

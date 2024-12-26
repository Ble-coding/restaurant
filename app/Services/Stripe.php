<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class Stripe
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUrl;

    public function __construct(string $apiKey, string $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->baseUrl = 'https://api.stripe.com/v1';
    }

    /**
     * Initialise un paiement et retourne l'URL de paiement ou les détails de la transaction.
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function initiatePayment(array $params): array
    {
        $this->validateParams($params);

        $payload = [
            'amount' => $params['amount'] * 100, // Montant en centimes pour Stripe
            'currency' => $params['currency'] ?? 'usd',
            'description' => $params['description'],
            'payment_method_types' => ['card'],
            'metadata' => $params['metadata'] ?? [],
        ];

        // Création d'un intent de paiement
        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->post("{$this->baseUrl}/payment_intents", $payload);

        if ($response->failed()) {
            \Log::error("Stripe API error", ['response' => $response->json()]);
            throw new Exception("Erreur Stripe: " . $response->json('error.message', 'Une erreur est survenue.'));
        }

        return $response->json();
    }

    /**
     * Récupère les détails d'une transaction Stripe.
     *
     * @param string $paymentIntentId
     * @return array
     * @throws Exception
     */
    public function getPaymentStatus(string $paymentIntentId): array
    {
        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->get("{$this->baseUrl}/payment_intents/{$paymentIntentId}");

        if ($response->failed()) {
            throw new Exception("Erreur Stripe: " . $response->json('error.message', 'Une erreur est survenue.'));
        }

        return $response->json();
    }

    /**
     * Valide les paramètres nécessaires pour initier un paiement.
     *
     * @param array $params
     * @throws Exception
     */
    protected function validateParams(array $params): void
    {
        $requiredFields = [
            'amount',
            'description',
        ];

        foreach ($requiredFields as $field) {
            if (empty($params[$field])) {
                throw new Exception("Erreur: Champ requis manquant - $field");
            }
        }
    }
}

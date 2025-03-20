<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;


class Stripe
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUrl;

    /**
     * Constructeur de la classe Stripe.
     *
     * @param string $apiKey
     * @param string $apiSecret
     */
    public function __construct(string $apiKey, string $apiSecret)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->baseUrl = 'https://api.stripe.com/v1';
    }

    public static function fromPaymentGateway($gateway): self
    {
        if (!$gateway || !$gateway->api_key || !$gateway->secret_key) {
            throw new Exception('Configuration Stripe invalide.');
        }

        return new self($gateway->api_key, $gateway->secret_key);
    }


    /**
     * Initialise un paiement et retourne l'intention de paiement.
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
     public function initiatePayment(array $params): array
    {
        // Validation des paramètres
        $this->validateParams($params);

        $payload = [
            'amount' => $params['amount'],
            'currency' => $params['currency'] ?? 'usd',
            'description' => $params['description'],
            'payment_method_types' => ['card'],
            'payment_method' => $params['payment_method'] ?? null,
        ];

        $response = Http::withBasicAuth($this->apiSecret, '')
            ->asForm()
            ->post("{$this->baseUrl}/payment_intents", $payload);

        if ($response->failed()) {
            throw new Exception("Erreur Stripe: " . $response->json('error.message'));
        }

        return $response->json();
    }

    /**
     * Récupère les détails d'une intention de paiement Stripe.
     *
     * @param string $paymentIntentId
     * @return array
     * @throws Exception
     */
    public function getPaymentStatus(string $paymentIntentId): array
    {
        // Envoyer une requête pour obtenir les détails de l'intention
        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->get("{$this->baseUrl}/payment_intents/{$paymentIntentId}");

        // Gestion des erreurs de l'API Stripe
        if ($response->failed()) {
            throw new Exception("Erreur Stripe: " . ($response->json('error.message') ?? 'Une erreur est survenue.'));
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
        $requiredFields = ['amount', 'description'];

        foreach ($requiredFields as $field) {
            if (empty($params[$field])) {
                throw new Exception("Erreur: Champ requis manquant - $field");
            }
        }

        if (!is_numeric($params['amount']) || $params['amount'] <= 0) {
            throw new Exception("Erreur: Le montant doit être un nombre positif.");
        }
    }

    public function confirmPaymentIntent(string $paymentIntentId): array
    {
        $response = Http::withBasicAuth($this->apiSecret, '')
            ->asForm()
            ->post("{$this->baseUrl}/payment_intents/{$paymentIntentId}/confirm");

        if ($response->failed()) {
            throw new Exception("Erreur Stripe lors de la confirmation : " . $response->json('error.message'));
        }

        return $response->json();
    }

}

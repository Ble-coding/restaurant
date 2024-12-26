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

    /**
     * Initialise un paiement et retourne l'intention de paiement.
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function initiatePayment(array $params): array
    {
        // Valider les paramètres nécessaires
        $this->validateParams($params);

        // Préparer le payload pour Stripe
        $payload = [
            'amount' => $params['amount'], // Montant en centimes
            'currency' => $params['currency'] ?? 'usd',
            'description' => $params['description'],
            'payment_method_types' => ['card'],
            'metadata' => $params['metadata'] ?? [],
        ];

        // Envoyer la requête à Stripe
        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->post("{$this->baseUrl}/payment_intents", $payload);

        // Gestion des erreurs de l'API Stripe
        if ($response->failed()) {
            \Log::error("Stripe API error", ['response' => $response->json()]);
            throw new Exception("Erreur Stripe: " . ($response->json('error.message') ?? 'Une erreur est survenue.'));
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
}

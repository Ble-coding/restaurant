<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class CinetPay
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $siteId;

    public function __construct(string $siteId, string $apiKey, string $version = 'v2')
    {
        $this->baseUrl = sprintf('https://api-checkout.cinetpay.com/%s/payment', strtolower($version));
        $this->apiKey = $apiKey;
        $this->siteId = $siteId;
    }

    /**
     * Initialise un paiement et retourne l'URL de paiement.
     *
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function initiatePayment(array $params): string
    {
        $this->validateParams($params);

        $payload = array_merge([
            'apikey' => $this->apiKey,
            'site_id' => $this->siteId,
            'currency' => $params['currency'] ?? 'XOF',
            'channels' => $params['channels'] ?? 'ALL',
            'metadata' => $params['metadata'] ?? '',
        ], $params);

        $response = Http::post("{$this->baseUrl}", $payload);

        if ($response->failed()) {
            \Log::error("CinetPay API error", ['response' => $response->json()]);
            throw new Exception("Erreur CinetPay: " . $response->json('message', 'Une erreur est survenue.'));
        }

        return $response->json('data.payment_url');
    }

    public function setTransaction(
        string $transactionId,
        float $amount,
        string $email,
        string $phone,
        string $description,
        string $firstName,
        string $lastName
    ) {
        $this->transaction = [
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'customer_email' => $email,
            'customer_phone_number' => $phone,
            'description' => $description,
            'customer_name' => $firstName,
            'customer_surname' => $lastName,
            'notify_url' => '', // Défini dans storeOrder
            'return_url' => '', // Défini dans storeOrder
        ];
    }


    public function getPaymentUrl(): string
    {
        if (empty($this->transaction)) {
            throw new Exception('Les détails de la transaction ne sont pas définis.');
        }

        return $this->initiatePayment($this->transaction);
    }


    /**
     * Vérifie le statut d'un paiement.
     *
     * @param string $transactionId
     * @return array
     * @throws Exception
     */
    public function getPaymentStatus(string $transactionId): array
    {
        $response = Http::post("{$this->baseUrl}/check", [
            'transaction_id' => $transactionId,
            'site_id' => $this->siteId,
            'apikey' => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new Exception("Erreur CinetPay: " . $response->json('message', 'Une erreur est survenue.'));
        }

        return $response->json('data');
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
            'transaction_id',
            'customer_name',
            'customer_surname',
            'description',
            'notify_url', // Champ requis pour la notification
            'return_url'  // Champ requis pour la redirection
        ];

        foreach ($requiredFields as $field) {
            if (empty($params[$field])) {
                throw new Exception("Erreur: Champ requis manquant - $field");
            }
        }
    }

}

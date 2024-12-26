<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{

    public function handle(Request $request)
    {
        $payload = $request->all();

        // Vérification de l'événement Stripe
        $event = $payload['type'] ?? null;

        if ($event === 'payment_intent.succeeded') {
            $paymentIntentId = $payload['data']['object']['id'];

            // Mettez à jour l'état de la commande
            $order = Order::where('payment_intent_id', $paymentIntentId)->first();
            if ($order) {
                $order->update(['status' => 'paid']);
            }
        }

        return response('Webhook Handled', 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\PaymentGateway;
use App\Models\User;

use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-payments')) {
            abort(403, 'Vous n\'avez pas la permission de voir cette page.');
        }
        $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur

        $payments = Payment::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc') // Optionnel : Trier les catégories par date
            ->paginate(6); // Pagination des résultats

        return view('admin.payments.index', compact('payments'));
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
            // Validation des données
            $validated = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            // Créer une nouvelle catégorie
            Payment::create($validated);

            return redirect()->route('admin.payments.index')->with('success', 'Libellé paiement crée avec succès!');
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
    public function update(Request $request, Payment $payment)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payments')->ignore($payment->id), // Unicité sauf pour l'ID actuel
            ],
        ]);

        // Mettre à jour le paiement
        $payment->update($validated);

        // Redirection avec un message de succès
        return redirect()->route('admin.payments.index')->with('success', 'Libellé du paiement mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        // Supprimer la paiement (vérifier si des articles y sont associés)
        $payment->delete();

        return redirect()->route('admin.payments.index')->with('success', 'Libellé paiement supprimé avec succès!');
    }


    /**
     * Affiche la liste des passerelles de paiement.
     */
    public function indexGateway(Request $request)
    {
        // Vérification des permissions
        if (!auth()->user()->can('view-gateways')) {
            abort(403, 'Vous n\'avez pas la permission de voir cette page.');
        }


        // Recherche et pagination
        $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur

        $payments = Payment::all();
        $gateways = PaymentGateway::query()
            ->when($search, function ($query) use ($search) {
                $query->where('api_key', 'like', '%' . $search . '%')
                      ->orWhere('site_id', 'like', '%' . $search . '%')
                      ->orWhere('secret_key', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc') // Trier par date de création
            ->paginate(6); // Pagination des résultats

        // Récupérer les utilisateurs avec les rôles admin et super_admin
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->get();

        return view('admin.payments.gateway', compact('gateways','users','payments'));
    }

    public function storeGateway(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'api_key'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_gateways', 'api_key'),
            ],
            'secret_key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_gateways', 'secret_key'),
            ],
        ]);

        try {
            DB::beginTransaction();  // Démarre la transaction

            // Récupère le paiement associé
            $payment = Payment::findOrFail($validated['payment_id']);

            // Vérifie le paiement et ajuste site_id
            if ($payment->name === "Stripe") {
                $validated['site_id'] = "0";
                $validated['callback_url'] = url('/payment/stripe/callback');
            } else {
                $validated['site_id'] = $request->validate([
                    'site_id' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('payment_gateways', 'site_id'),
                    ],
                ])['site_id'];
                $validated['callback_url'] = url('/payment/cinetpay/callback');
            }

            // Crée la passerelle de paiement
            $paymentGateway = PaymentGateway::create($validated);

            DB::commit();  // Valide la transaction
            return back()->with('success', 'Passerelle ajoutée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();  // Annule la transaction en cas d'erreur
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour une passerelle de paiement existante.
     */
    public function updateGateway(Request $request, PaymentGateway $gateway)
    {
        // Validation des champs de la requête
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'api_key'    => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_gateways', 'api_key')->ignore($gateway->id),
            ],
            'secret_key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_gateways', 'secret_key')->ignore($gateway->id),
            ],
        ]);

        try {
            DB::beginTransaction(); // Démarre la transaction

            // Récupère le paiement associé
            $payment = Payment::findOrFail($validated['payment_id']);

            // Vérifie le paiement et ajuste site_id et callback_url
            if ($payment->name === "Stripe") {
                $validated['site_id'] = "0";
                $validated['callback_url'] = url('/payment/stripe/callback');
            } else {
                $validated = array_merge($validated, $request->validate([
                    'site_id' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('payment_gateways', 'site_id')->ignore($gateway->id),
                    ],
                ]));
                $validated['callback_url'] = url('/payment/cinetpay/callback');
            }

            // Met à jour la passerelle de paiement
            $gateway->update($validated);

            DB::commit(); // Valide la transaction
            return back()->with('success', 'Passerelle mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack(); // Annule la transaction en cas d'erreur
            return back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }


     /**
     * Supprime une passerelle de paiement.
     */
    public function destroyGateway(PaymentGateway $gateway)
    {
        // Suppression de la passerelle
        $gateway->delete();

        return redirect()->route('admin.gateways.index')->with('success', 'La passerelle de paiement a été supprimée avec succès!');
    }

}

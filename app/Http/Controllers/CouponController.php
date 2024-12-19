<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Coupon;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-coupons')) {
            abort(403, 'Vous n\'avez pas la permission de voir cette page.');
        }
        // Récupérer les paramètres de recherche
        $search = $request->get('search');
        $expired = $request->get('expired');

        // Filtrer les coupons
        $coupons = Coupon::query() // Inclure tous les coupons par défaut
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', '%' . $search . '%')
                             ->orWhere('expires_at', 'like', '%' . $search . '%');
                });
            })
            ->when($expired, function ($query, $expired) {
                if ($expired === 'expired') {
                    // Inclure uniquement les coupons expirés
                    return $query->whereNotNull('expires_at')
                                 ->where('expires_at', '<', now());
                } elseif ($expired === 'active') {
                    // Inclure uniquement les coupons actifs
                    return $query->where(function ($subQuery) {
                        $subQuery->whereNull('expires_at')
                                 ->orWhere('expires_at', '>', now());
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Retourner la vue avec tous les coupons, y compris les inactifs
        return view('admin.coupons.index', compact('coupons'));
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
        // Validation des données envoyées par l'utilisateur
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount' => 'required|numeric',
            'type' => 'required|string',
            'expires_at' => 'nullable|date',
        ]);

        // Création du coupon avec les données validées
        $coupon = Coupon::create($request->only(['code', 'discount', 'type', 'expires_at']));

        // Définir le statut du coupon en fonction de sa validité
        $coupon->status = $coupon->isValid() ? 'active' : 'inactive';
        $coupon->save(); // Sauvegarder après mise à jour

        // Rediriger avec un message de succès
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon créé avec succès.');
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
    public function update(Request $request, Coupon $coupon)
    {
        // Validation des données envoyées par l'utilisateur
        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric',
            'type' => 'required|string',
            'expires_at' => 'nullable|date',
        ]);

        // Mettre à jour les champs du coupon avec les données validées
        $coupon->fill($request->only(['code', 'discount', 'type', 'expires_at']));

        // Définir le statut du coupon après mise à jour
        $coupon->status = $coupon->isValid() ? 'active' : 'inactive';
        $coupon->save(); // Sauvegarder après mise à jour

        // Rediriger avec un message de succès
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon mis à jour avec succès.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon supprimé avec succès.');
    }
}

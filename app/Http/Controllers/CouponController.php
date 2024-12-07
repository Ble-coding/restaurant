<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Coupon;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $expired = $request->get('expired'); // Paramètre pour savoir si le coupon est expiré

        // Appliquer le filtrage si un terme de recherche est présent
        $coupons = Coupon::when($search, function ($query, $search) {
            return $query->where('code', 'like', '%' . $search . '%')
                         ->orWhere('type', 'like', '%' . $search . '%');
        })
        ->when($expired, function ($query, $expired) {
            if ($expired == 'expired') {
                return $query->whereNotNull('expires_at')
                             ->where('expires_at', '<', now());
            } elseif ($expired == 'active') {
                return $query->whereNull('expires_at')
                             ->orWhere('expires_at', '>', now());
            }
        })
        ->paginate(5);

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
        $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'discount' => 'required|numeric',
            'type' => 'required|string',
            'expires_at' => 'nullable|date',
        ]);

        // Créer un coupon
        $coupon = Coupon::create($request->all());

        // Définir le statut du coupon après sa création
        $coupon->status = $coupon->isValid() ? 'active' : 'inactive';
        $coupon->save(); // Sauvegarder après la mise à jour du statut

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
        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric',
            'type' => 'required|string',
            'expires_at' => 'nullable|date',
        ]);

        // Mettre à jour les données du coupon
        $coupon->update($request->all());

        // Mettre à jour le statut du coupon après modification
        $coupon->status = $coupon->isValid() ? 'active' : 'inactive';
        $coupon->save(); // Sauvegarder après la mise à jour du statut

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

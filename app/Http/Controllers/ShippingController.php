<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipping;
use Illuminate\Validation\Rule;

class ShippingController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-shippings')) {
            abort(403, 'Vous n\'avez pas la permission de voir cette page.');
        }
        $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur

        $shippings = Shipping::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc') // Optionnel : Trier les catégories par date
            ->paginate(6); // Pagination des résultats

        return view('admin.payments.shippings', compact('shippings'));
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
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:shippings',
            'price' => 'required|numeric',
        ]);

        Shipping::create($validated);

        return redirect()->route('admin.shippings.index')->with('success', 'Option de livraison ajoutée avec succès!');
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
    public function update(Request $request, Shipping $shipping){
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('shippings')->ignore($shipping->id)],
            'price' => 'required|numeric',
        ]);

        $shipping->update($validated);

        return redirect()->route('admin.shippings.index')->with('success', 'Option de livraison mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipping $shipping)
    {
        $shipping->delete();

        return redirect()->route('admin.shippings.index')->with('success', 'Option de livraison supprimée avec succès!');
    }
}

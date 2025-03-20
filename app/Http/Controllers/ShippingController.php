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
    // public function index(Request $request)
    // {
    //     if (!auth()->user()->can('view-shippings')) {
    //         // abort(403, 'Vous n\'avez pas la permission de voir cette page.');
    //         abort(403, __('shippings.forbidden'));
    //     }
    //     $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur

    //     $shippings = Shipping::query()
    //         ->when($search, function ($query) use ($search) {
    //             $query->where('name', 'like', '%' . $search . '%');
    //         })
    //         ->orderBy('created_at', 'desc') // Optionnel : Trier les catégories par date
    //         ->paginate(6); // Pagination des résultats

    //     return view('admin.payments.shippings', compact('shippings'));
    // }

    public function index(Request $request)
    {
        if (!auth()->user()->can('view-shippings')) {
            abort(403, __('shippings.forbidden'));
        }

        $search = trim($request->get('search'));

        $shippings = Shipping::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name->fr', 'like', "%$search%")
                    ->orWhere('name->en', 'like', "%$search%")
                    ->orWhere('type', 'like', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(6);

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
            'name.fr' => 'required|string|max:255|unique:shippings,name->fr',
            'name.en' => 'required|string|max:255|unique:shippings,name->en',
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|in:free,paid,conditional',
            'min_price_for_free' => 'nullable|numeric|min:0',
            'conditions' => 'nullable|array', // On accepte un tableau JSON
        ]);

        $shipping = new Shipping();
        $shipping->setTranslations('name', [
            'fr' => $validated['name']['fr'],
            'en' => $validated['name']['en'],
        ]);
        $shipping->price = $validated['price'];
        $shipping->type = $validated['type'];
        $shipping->min_price_for_free = $validated['min_price_for_free'] ?? null;
        $shipping->conditions = $validated['conditions'] ?? [];
        $shipping->save();

        return redirect()->route('admin.shippings.index')
            ->with('success', __('shippings.success_add'));
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
    public function edit(Shipping $shipping)
    {
        return view('admin.payments.shippingsEdit', compact('shipping'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipping $shipping)
    {
        $validated = $request->validate([
            'name.fr' => ['required', 'string', 'max:255', Rule::unique('shippings', 'name->fr')->ignore($shipping->id)],
            'name.en' => ['required', 'string', 'max:255', Rule::unique('shippings', 'name->en')->ignore($shipping->id)],
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|in:free,paid,conditional',
            'min_price_for_free' => 'nullable|numeric|min:0',
            'conditions' => 'nullable|array',
        ]);

        $shipping->setTranslations('name', [
            'fr' => $validated['name']['fr'],
            'en' => $validated['name']['en'],
        ]);
        $shipping->price = $validated['price'];
        $shipping->type = $validated['type'];
        $shipping->min_price_for_free = $validated['min_price_for_free'] ?? null;
        $shipping->conditions = $validated['conditions'] ?? [];
        $shipping->save();

        return redirect()->route('admin.shippings.index')
            ->with('success', __('shippings.success_update'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipping $shipping)
    {
        $shipping->delete();

        return redirect()->route('admin.shippings.index')
        ->with('success', __('shippings.success_delete'));
        // ->with('success', 'Option de livraison supprimée avec succès!');
    }
}

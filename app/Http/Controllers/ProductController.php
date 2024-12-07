<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Pour gérer la requête HTTP
use App\Models\Product; // Pour manipuler le modèle Product
use Illuminate\Support\Facades\Storage; // Pour gérer le stockage de fichiers (images)
use Illuminate\Support\Facades\Validator; // Si tu utilises la validation manuelle

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer la valeur de la recherche depuis la requête GET
        $search = $request->get('search');

        // Appliquer le filtrage si un terme de recherche est présent
        $products = Product::when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                         ->orWhere('description', 'like', '%' . $search . '%')
                         ->orWhere('status', 'like', '%' . $search . '%'); // Exemple de champ supplémentaire
        })
        ->paginate(10); // Paginer les résultats (10 par page)

        return view('admin.products.index', compact('products'));
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
        // Validation des champs
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|string|in:available,recommended,seasonal',
        ]);

        // Gestion de l'upload de l'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Création du produit
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => isset($imagePath) ? $imagePath : null,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produit ajouté avec succès.');
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
    public function update(Request $request, Product $product)
    {
        // Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation de l'image
            'status' => 'required|string|in:available,recommended,seasonal',
        ]);

        // Mise à jour du produit
        $product->name = $validatedData['name'];
        $product->description = $validatedData['description'];
        $product->price = $validatedData['price'];
        $product->status = $validatedData['status'];

        // Si une image a été téléchargée
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image); // Supprime l'ancienne image
            }

            // Enregistrer la nouvelle image
            $product->image = $request->file('image')->store('products', 'public');
        }

        // Sauvegarder le produit mis à jour
        $product->save();

        // Rediriger avec un message de succès
        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Suppression de l'image si elle existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Suppression du produit
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé avec succès.');
    }

}

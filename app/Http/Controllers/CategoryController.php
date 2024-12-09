<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur

        $categories = Category::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc') // Optionnel : Trier les catégories par date
            ->paginate(6); // Pagination des résultats

        return view('admin.articles.category.index', compact('categories'));
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
        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie créée avec succès!');
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
    public function update(Request $request, Category $category)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Mettre à jour la catégorie
        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Supprimer la catégorie (vérifier si des articles y sont associés)
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée avec succès!');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-categories')) {
            abort(403, 'Vous n\'avez pas la permission de voir cette page.');
        }
        $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur

        $categories = Category::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc') // Optionnel : Trier les catégories par date
            ->paginate(6); // Pagination des résultats

        return view('admin.category.index', compact('categories'));
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
            // 'name' => 'required|string|max:255',
            'name' => 'required|array', // Le champ 'name' doit être un tableau
            'name.*' => 'required|string|max:255',
        ]);

        // Créer une nouvelle catégorie
        // Category::create($validated);

          // Créer une nouvelle catégorie avec les traductions
        $category = new Category();
        $category->setTranslations('name', $validated['name']);
        $category->save();

        return redirect()->route('admin.categories.index')
        ->with('success', __('category.success.created'));
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
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
        ]);

        // Mettre à jour les traductions de la catégorie
        $category->setTranslations('name', $validated['name']);
        $category->save();

        return redirect()->route('admin.categories.index')
        ->with('success', __('category.success.updated'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Supprimer la catégorie (vérifier si des articles y sont associés)
        // $category->delete();

        // return redirect()->route('admin.categories.index')->with('success', 'Catégorie supprimée avec succès!');


        try {
            $category->delete();
            return redirect()->route('admin.categories.index')
                ->with('success', __('category.success.deleted'));
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', __('category.error.delete_failed'));
        }

    }
}

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
            abort(403, __('category.forbidden'));
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
            'name' => 'required|array', // Le champ 'name' doit être un tableau
            'name.*' => 'required|string|max:255',
        ]);

        // Génération des slugs pour chaque langue
        $slugs = [];
        foreach ($validated['name'] as $lang => $name) {
            $slugs[$lang] = Str::slug($name);
        }

        // Créer une nouvelle catégorie avec les traductions
        $category = new Category();
        $category->setTranslations('name', $validated['name']);
        $category->setTranslations('slug', $slugs);
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

        // Récupérer les anciennes traductions
        $oldTranslations = $category->getTranslations('name');

        // Mettre à jour les traductions du nom
        $category->setTranslations('name', $validated['name']);

        // Générer les nouveaux slugs uniquement pour les langues modifiées
        $newSlugs = [];
        foreach ($validated['name'] as $lang => $newName) {
            if (!isset($oldTranslations[$lang]) || $oldTranslations[$lang] !== $newName) {
                // Si la traduction a changé, on régénère le slug
                $newSlugs[$lang] = Str::slug($newName);
            } else {
                // Sinon, on garde l'ancien slug
                $newSlugs[$lang] = $category->getTranslation('slug', $lang);
            }
        }

        // Mettre à jour les slugs
        $category->setTranslations('slug', $newSlugs);

        // Sauvegarder la catégorie
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

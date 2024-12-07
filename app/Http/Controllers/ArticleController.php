<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categories = Category::all();

        // Appliquer la recherche si un terme est spécifié
        $articles = Blog::when($search, function ($query, $search) {
            return $query->where('title', 'like', '%' . $search . '%')
                         ->orWhere('content', 'like', '%' . $search . '%')
                         ->orWhere('category_id', 'like', '%' . $search . '%')
                         ->orWhere('status', 'like', '%' . $search . '%');
        })
        ->paginate(6); // Pagination des résultats (10 par page)

        return view('admin.articles.index', compact('articles','categories'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $categories = Category::all();

        return view('admin.articles.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'content' => 'required|string',
            'status' => 'required|in:' . implode(',', array_keys(Blog::STATUSES)),
        ]);

        // Gérer le téléchargement de l'image
          $imagePath = $this->handleImageUpload($request);

        $slug = Blog::generateSlug($validated['title']);
        // Créer l'article
        Blog::create([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'image' => $imagePath,
           'slug' => $slug,
           'status' => $validated['status'],
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Article créé avec succès!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Blog $article)
    {
        return view('admin.articles.show', compact('article'));
    }

    // Méthode pour afficher le formulaire d'édition d'un article
    public function edit(Blog $article)
    {
        $categories = Category::all();
        return view('admin.articles.edit', compact('article','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $article)
    {
        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'content' => 'required|string',
            'status' => 'required|in:' . implode(',', array_keys(Blog::STATUSES)),
        ]);

        $imagePath = $this->handleImageUpload($request, $article->image);

        // Générer un nouveau slug si le titre a changé
        $slug = $validated['title'] !== $article->title
        ? Blog::generateSlug($validated['title'])
        : $article->slug;

        // Mettre à jour l'article
        $article->update([
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'image' => $imagePath,
            'slug' => $slug,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Article mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $article)
    {
        // Supprimer l'image si elle existe
        if ($article->image) {
            Storage::delete('public/' . $article->image);
        }

        // Supprimer l'article
        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article supprimé avec succès!');
    }

    protected function handleImageUpload(Request $request, $existingImage = null)
    {
        if ($request->hasFile('image')) {
            // Supprimer l'image existante si elle existe
            if ($existingImage) {
                Storage::delete('public/' . $existingImage);
            }

            // Stocker la nouvelle image et retourner son chemin
            return $request->file('image')->store('articles/images', 'public');
        }

        // Retourner l'image existante si aucune nouvelle image n'a été téléchargée
        return $existingImage;
    }

}

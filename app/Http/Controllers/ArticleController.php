<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('permission:read-blogs')->only('index');
    //     $this->middleware('permission:create-blogs')->only('create', 'store');
    //     $this->middleware('permission:edit-blogs')->only('edit', 'update');
    //     $this->middleware('permission:delete-blogs')->only('destroy');
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('view-articles') && !auth()->user()->can('view-blogs')) {
            abort(403, 'Vous n\'avez pas la permission de voir cette page.');
        }
        $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur
        $categories = Category::all(); // Récupérer toutes les catégories

        // Appliquer la recherche sur les articles
        $articles = Blog::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', '%' . $search . '%') // Rechercher dans le titre
                            ->orWhere('content', 'like', '%' . $search . '%') // Rechercher dans le contenu
                            ->orWhere('status', 'like', '%' . $search . '%'); // Rechercher dans le statut
                })
                ->orWhereHas('category', function ($query) use ($search) { // Rechercher dans les catégories liées
                    $query->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc') // Trier par date de création, la plus récente en premier
            ->paginate(6); // Pagination avec 6 articles par page

        return view('admin.articles.index', compact('articles', 'categories'));
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

        $commentsCount = $article->comments()->count();

        $article->load('comments');
        // Récupérer les 3 derniers blogs publiés, en excluant l'actuel
        $latestBlogs = Blog::where('status', 'published')
            ->where('id', '!=', $article->id) // Exclure le blog actuel
            ->orderBy('created_at', 'desc')
            ->take(3) // Limiter à 3 blogs
            ->get();
         return view('admin.articles.show', compact('article', 'latestBlogs','commentsCount'));
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

    public function storeComment(Request $request, Blog $article)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'content' => 'required|string',
            'country_code' => ['required', 'string'],
            'phone' => 'required|string|max:20', // Nouveau champ ajouté
            'save_info' => 'nullable|boolean', // Gérer le champ save_info
        ]);

        // dd($request);
        $article->comments()->create($validated);

        return redirect()->route('admin.articles.show', $blog->id)->with('success', 'Votre commentaire a été ajouté avec succès.');
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

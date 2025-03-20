<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;

use App\Models\Category;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupération des filtres
        $search = trim($request->get('search'));
        $categoryId = $request->get('category_id'); // Facultatif si tu veux filtrer par catégorie
        $status = $request->get('status'); // Facultatif si tu veux autoriser d'autres statuts (ex: drafts visibles pour admin connecté)
    
        // Récupération des catégories si nécessaire (pour afficher dans la vue)
        $categories = Category::all();
    
        // Construction de la requête
        $blogs = Blog::query()
            ->with('category') // Si tu veux afficher la catégorie
            ->withCount('comments') // Compter les commentaires
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', '%' . $search . '%')
                             ->orWhere('content', 'like', '%' . $search . '%');
                });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            }, function ($query) {
                $query->where('status', 'published'); // Par défaut, uniquement les blogs publiés
            })
            ->orderBy('created_at', 'desc')
            ->paginate(4);
    
        // Retourner la vue avec filtres
        return view('blogs.index', compact('blogs', 'categories'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        $commentsCount = $blog->comments()->count();

        // Récupérer les 3 derniers blogs publiés, en excluant l'actuel
        $latestBlogs = Blog::where('status', 'published')
            ->where('id', '!=', $blog->id) // Exclure le blog actuel
            ->orderBy('created_at', 'desc')
            ->take(3) // Limiter à 3 blogs
            ->get();

        return view('blogs.show', compact('blog', 'latestBlogs','commentsCount'));
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function storeComment(Request $request, Blog $blog)
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
        $blog->comments()->create($validated);

        return redirect()->route('blogs.show', $blog->id)->with('success', 'Votre commentaire a été ajouté avec succès.');
    }

}

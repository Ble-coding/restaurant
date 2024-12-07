<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Blog;

class HomeController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer les produits (tu peux ajouter des filtres si nécessaire)
        $products = Product::all(); // Récupérer tous les produits

        // Récupérer les 4 blogs récents ayant le statut "published"
        $blogs = Blog::where('status', 'published')
            ->orderBy('created_at', 'desc') // Trier par les plus récents
            ->limit(4) // Limiter à 4 blogs
            ->get();

        // Retourner la vue avec les données
        return view('home', compact('products', 'blogs'));
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
    // public function show(string $id)
    // {
    //     //
    // }

    public function show(Blog $blog)
    {
        // Récupérer les 3 derniers blogs publiés, en excluant l'actuel
        $latestBlogs = Blog::where('status', 'published')
            ->where('id', '!=', $blog->id) // Exclure le blog actuel
            ->orderBy('created_at', 'desc')
            ->take(3) // Limiter à 3 blogs
            ->get();

        return view('blogs.show', compact('blog', 'latestBlogs'));
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
}

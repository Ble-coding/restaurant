<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Category;

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
         // Définir dynamiquement les mots-clés pour filtrer les catégories
        $platKeywords = ['Plat', 'Menu'];
        $boissonKeywords = ['Boisson', 'Naturelles','Portion', 'Accompagnement', 'Fruit'];

        // Récupérer dynamiquement les slugs des catégories correspondant à "Plats"
        $slugsPlats = Category::where(function ($query) use ($platKeywords) {
            foreach ($platKeywords as $keyword) {
                $query->orWhere('name', 'LIKE', "%$keyword%");
            }
        })
        ->pluck('slug')
        ->toArray();

        // Récupérer dynamiquement les slugs des catégories correspondant à "Boissons"
        $slugsBoissons = Category::where(function ($query) use ($boissonKeywords) {
            foreach ($boissonKeywords as $keyword) {
                $query->orWhere('name', 'LIKE', "%$keyword%");
            }
        })
        ->pluck('slug')
        ->toArray();

        // Récupérer les produits associés aux catégories "Plats" avec pagination
        $productsMenusPlats = Product::query()
            ->whereHas('category', function ($query) use ($slugsPlats) {
                $query->whereIn('slug', $slugsPlats);
            })
            ->paginate(10); // Pagination à 10 produits par page

        // Récupérer les produits associés aux catégories "Boissons" avec pagination
        $productsMenusBoissons = Product::query()
            ->whereHas('category', function ($query) use ($slugsBoissons) {
                $query->whereIn('slug', $slugsBoissons);
            })
            ->paginate(10); // Pagination à 10 produits par page


            // Récupérer les 4 blogs récents ayant le statut "published"
            $blogs = Blog::withCount('comments')
                ->where('status', 'published')
                ->orderBy('created_at', 'desc') // Trier par les plus récents
                ->limit(4) // Limiter à 4 blogs
                ->get();

            // Retourner la vue avec les données
            return view('home', compact('productsMenusPlats', 'blogs', 'productsMenusBoissons'));
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

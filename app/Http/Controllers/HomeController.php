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
    // public function index()
    // {
    //      // Définir dynamiquement les mots-clés pour filtrer les catégories
    //     $platKeywords = ['Plat', 'Menu'];
    //     $boissonKeywords = ['Boisson', 'Naturelles','Portion', 'Accompagnement', 'Fruit'];

    //     // Récupérer dynamiquement les slugs des catégories correspondant à "Plats"
    //     $slugsPlats = Category::where(function ($query) use ($platKeywords) {
    //         foreach ($platKeywords as $keyword) {
    //             $query->orWhere('name', 'LIKE', "%$keyword%");
    //         }
    //     })
    //     ->pluck('slug')
    //     ->toArray();

    //     // Récupérer dynamiquement les slugs des catégories correspondant à "Boissons"
    //     $slugsBoissons = Category::where(function ($query) use ($boissonKeywords) {
    //         foreach ($boissonKeywords as $keyword) {
    //             $query->orWhere('name', 'LIKE', "%$keyword%");
    //         }
    //     })
    //     ->pluck('slug')
    //     ->toArray();

    //     // Récupérer les produits associés aux catégories "Plats" avec pagination
    //     $productsMenusPlats = Product::query()
    //         ->whereHas('category', function ($query) use ($slugsPlats) {
    //             $query->whereIn('slug', $slugsPlats);
    //         })
    //         ->paginate(10); // Pagination à 10 produits par page

    //     // Récupérer les produits associés aux catégories "Boissons" avec pagination
    //     $productsMenusBoissons = Product::query()
    //         ->whereHas('category', function ($query) use ($slugsBoissons) {
    //             $query->whereIn('slug', $slugsBoissons);
    //         })
    //         ->paginate(10); // Pagination à 10 produits par page


    //         // Récupérer les 4 blogs récents ayant le statut "published"
    //         $blogs = Blog::withCount('comments')
    //             ->where('status', 'published')
    //             ->orderBy('created_at', 'desc') // Trier par les plus récents
    //             ->limit(4) // Limiter à 4 blogs
    //             ->get();

    //         // Retourner la vue avec les données
    //         return view('home', compact('productsMenusPlats', 'blogs', 'productsMenusBoissons'));
    // }




    public function index(Request $request)
    {
        $locale = app()->getLocale();
       
        $categories = Category::all();
        // Récupérer les statuts possibles
    $statuses = Product::getStatusLabels(); // Fonction qui retourne les statuts

    // Récupérer les catégories sélectionnées
    $selectedCategories = $request->input('categories', []);
    
    // Récupérer les statuts sélectionnés
    $selectedStatuses = $request->input('statuses', []);
    
        // Filtrer les produits par `category_id`
       // Filtrer les produits par `category_id` et `status`
    $products = Product::when(!empty($selectedCategories), function ($query) use ($selectedCategories) {
        $query->whereIn('category_id', $selectedCategories);
    })->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
        $query->whereIn('status', $selectedStatuses);
    })->get();

    

        // Récupérer dynamiquement les slugs des catégories correspondant aux "Plats"
        // $slugsPlats = Category::select('slug')
            // ->where(function ($query) use ($locale) {
            //     $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%plat%'])
            //         ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%menu%']);
            // })
            // ->get()
            // ->map(function ($category) use ($locale) {
            //     $decodedSlug = json_decode($category->slug, true);
            //     return $decodedSlug[$locale] ?? null;
            // })
            // ->filter()
            // ->toArray();

        // Récupérer dynamiquement les slugs des catégories correspondant aux "Boissons"
        // $slugsBoissons = Category::select('slug')
            // ->where(function ($query) use ($locale) {
            //     $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%boisson%'])
            //         ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%naturelles%'])
            //         ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%portion%'])
            //         ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%accompagnement%'])
            //         ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%fruit%']);
            // })
            // ->get()
            // ->map(function ($category) use ($locale) {
            //     $decodedSlug = json_decode($category->slug, true);
            //     return $decodedSlug[$locale] ?? null;
            // })
            // ->filter()
            // ->toArray();

        // Récupérer les produits associés aux catégories "Plats"
        // $productsMenusPlats = Product::query()
        //     ->whereHas('category', function ($query) use ($slugsPlats) {
        //         $query->whereIn('slug', $slugsPlats);
        //     })
        //     ->paginate(10);

        // Récupérer les produits associés aux catégories "Boissons"
        // $productsMenusBoissons = Product::query()
        //     ->whereHas('category', function ($query) use ($slugsBoissons) {
        //         $query->whereIn('slug', $slugsBoissons);
        //     })
        //     ->paginate(10);

            // dd($productsMenusBoissons);

        // Récupérer les 4 blogs récents ayant le statut "published"
        $blogs = Blog::withCount('comments')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Retourner la vue avec les données
        return view('home', compact('products', 'blogs', 'categories', 'statuses'));
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

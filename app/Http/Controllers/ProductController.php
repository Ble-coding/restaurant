<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Pour gérer la requête HTTP
use App\Models\Product; // Pour manipuler le modèle Product
use Illuminate\Support\Facades\Storage; // Pour gérer le stockage de fichiers (images)
use Illuminate\Support\Facades\Validator; // Si tu utilises la validation manuelle
use App\Models\Category;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{

    /**   abort(403, __('product.forbidden'));
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     // Vérifie si l'utilisateur possède les permissions
    //     if (!auth()->user()->can('view-menus') && !auth()->user()->can('view-products')) {
    //         abort(403, 'Vous n\'avez pas la permission de voir cette page.');
    //     }

    //     // Nettoyer l'entrée utilisateur
    //     $search = trim($request->get('search'));
    //     $status = $request->get('status');
    //     $categoryId = $request->get('category_id');
    //     $price = $request->get('price');

    //     // Définir les mots-clés associés aux catégories spéciales
    //     $livraisonKeywords = ['Livraison', 'Livraisons'];
    //     $boissonKeywords = ['Boisson', 'Naturelles', 'Fruit', 'Fruits'];

    //     // Récupérer les slugs des catégories "Boissons"
    //     $slugsBoissons = Category::where(function ($query) use ($boissonKeywords) {
    //         foreach ($boissonKeywords as $keyword) {
    //             $query->orWhere('name', 'LIKE', "%$keyword%");
    //         }
    //     })
    //     ->pluck('slug')
    //     ->toArray();

    //     // Récupérer les slugs des catégories "Livraison"
    //     $slugsLivraison = Category::where(function ($query) use ($livraisonKeywords) {
    //         foreach ($livraisonKeywords as $keyword) {
    //             $query->orWhere('name', 'LIKE', "%$keyword%");
    //         }
    //     })
    //     ->pluck('slug')
    //     ->toArray();

    //     // Appliquer les filtres de recherche
    //     $products = Product::query()
    //         ->when($search, function ($query) use ($search) {
    //             $query->where(function ($subQuery) use ($search) {
    //                 $subQuery->where('name', 'like', "%$search%")
    //                         ->orWhere('description', 'like', "%$search%");
    //             });
    //         })
    //         ->when($status, function ($query) use ($status) {
    //             $query->where('status', $status);
    //         })
    //         ->when($categoryId, function ($query) use ($categoryId) {
    //             $query->where('category_id', $categoryId);
    //         })
    //         ->when($price, function ($query) use ($price) {
    //             $query->where(function ($subQuery) use ($price) {
    //                 $subQuery->where('price', 'like', "%$price%")
    //                         ->orWhere('price_half_litre', 'like', "%$price%")
    //                         ->orWhere('price_litre', 'like', "%$price%");
    //             });
    //         })
    //         ->whereHas('category', function ($query) use ($slugsLivraison) {
    //             $query->whereNotIn('slug', $slugsLivraison);
    //         })
    //         ->with('category')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(4);

    //     // Récupérer toutes les catégories sauf celles associées aux "Livraisons"
    //     $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

    //     return view('admin.products.index', compact('products', 'categories', 'slugsBoissons'));
    // }

    public function index(Request $request)
    {
        // Vérifie si l'utilisateur possède les permissions
        if (!auth()->user()->can('view-menus') && !auth()->user()->can('view-products')) {
            abort(403, __('product.forbidden'));
        }

        // Nettoyer l'entrée utilisateur
        $search = trim($request->get('search'));
        $status = $request->get('status');
        $categoryId = $request->get('category_id');
        $price = $request->get('price');

        $locale = app()->getLocale();

        // $slugsBoissons = Category::where(function ($query) use ($locale) {
        //     $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.$locale')) LIKE ?", ['%boisson%'])
        //           ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.$locale')) LIKE ?", ['%drink%']);
        // })
        // ->pluck('slug')
        // ->map(function ($slug) use ($locale) {
        //     return json_decode($slug, true)[$locale] ?? null;
        // })
        // ->filter()
        // ->values()
        // ->toArray();

        $slugsBoissons = Category::select('slug') // Sélectionne uniquement le champ slug
        ->where(function ($query) use ($locale) {
            $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%boisson%'])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%drink%']);
        })
        ->get() // Récupère un tableau d'objets Category
        ->map(function ($category) use ($locale) {
            $decodedSlug = json_decode($category->slug, true); // Décoder le JSON
            return $decodedSlug[$locale] ?? null; // Récupérer la valeur correspondant à la langue actuelle
        })
        ->filter()
        ->toArray();




        // $slugsBoissons = Category::all();

        // dd($slugsBoissons);


        // Récupération des slugs des catégories "Livraison" en fonction de la langue actuelle
        $slugsLivraison = Category::select('slug')
        ->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%livraison%'])
        ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%delivery%'])
        ->get() // Récupère des objets Category
        ->map(function ($category) use ($locale) {
            $decodedSlug = json_decode($category->slug, true); // Décoder le JSON
            return $decodedSlug[$locale] ?? null; // Récupérer la valeur correspondant à la langue actuelle
        })
        ->filter()
        ->toArray();
        // dd(Category::select('slug')->first());

        // dd($slugsLivraison);
        // Appliquer les filtres de recherche
        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("JSON_EXTRACT(name, '$.fr') LIKE ?", ["%$search%"])
                      ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%$search%"])
                      ->orWhereRaw("JSON_EXTRACT(description, '$.fr') LIKE ?", ["%$search%"])
                      ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ["%$search%"]);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($price, function ($query) use ($price) {
                $query->where(function ($subQuery) use ($price) {
                    $subQuery->where('price', 'like', "%$price%")
                             ->orWhere('price_half_litre', 'like', "%$price%")
                             ->orWhere('price_litre', 'like', "%$price%");
                });
            })
            ->whereHas('category', function ($query) use ($slugsLivraison) {
                $query->whereNotIn('slug', $slugsLivraison);
            })
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(4);

        // Récupérer toutes les catégories sauf celles associées aux "Livraisons"
        $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

        return view('admin.products.index', compact('products', 'categories', 'slugsBoissons'));
    }



    // public function index(Request $request)
    // {

    //     // Vérifie si l'utilisateur possède les permissions
    //     if (!auth()->user()->can('view-menus') && !auth()->user()->can('view-products')) {
    //         abort(403, 'Vous n\'avez pas la permission de voir cette page.');
    //     }
    //     // Nettoyer l'entrée utilisateur pour éviter les espaces inutiles
    //     $search = trim($request->get('search'));

    //     // Définir les mots-clés associés à la catégorie "Livraisons"
    //     $livraisonKeywords = ['Livraison', 'Livraisons'];

    //     $boissonKeywords = ['Boisson', 'Naturelles', 'Fruit', 'Fruits'];

    //      // Récupérer dynamiquement les slugs des catégories correspondant à "Boissons"
    //      $slugsBoissons = Category::where(function ($query) use ($boissonKeywords) {
    //         foreach ($boissonKeywords as $keyword) {
    //             $query->orWhere('name', 'LIKE', "%$keyword%");
    //         }
    //     })
    //     ->pluck('slug')
    //     ->toArray();

    //     // Récupérer dynamiquement les slugs des catégories correspondant aux mots-clés
    //     $slugsLivraison = Category::where(function ($query) use ($livraisonKeywords) {
    //         foreach ($livraisonKeywords as $keyword) {
    //             $query->orWhere('name', 'LIKE', "%$keyword%");
    //         }
    //     })
    //     ->pluck('slug')
    //     ->toArray();

    //     // Appliquer les filtres de recherche
    //     $products = Product::query()
    //         ->when($search, function ($query) use ($search) {
    //             $query->where(function ($subQuery) use ($search) {
    //                 $subQuery->where('name', 'like', '%' . $search . '%')
    //                          ->orWhere('description', 'like', '%' . $search . '%')
    //                          ->orWhere('status', 'like', '%' . $search . '%');
    //             })
    //             ->orWhereHas('category', function ($query) use ($search) {
    //                 $query->where('name', 'like', '%' . $search . '%');
    //             });
    //         })
    //         // Exclure les catégories associées aux slugs des "Livraisons"
    //         ->whereHas('category', function ($query) use ($slugsLivraison) {
    //             $query->whereNotIn('slug', $slugsLivraison);
    //         })
    //         ->with('category')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(6);

    //     // Récupérer toutes les catégories sauf celles associées aux "Livraisons"
    //     $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

    //     // Retourner les données à la vue
    //     return view('admin.products.index', compact('products', 'categories','slugsBoissons'));
    // }

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
    // public function store(Request $request)
    // {
    //     // Validation des champs
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'category_id' => 'required|exists:categories,id',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
    //         'status' => 'required|string|in:available,recommended,seasonal',
    //         'price' => [
    //             'nullable',
    //             'numeric',
    //             'min:0',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 if ($request->category_id && Category::find($request->category_id)->slug === 'boissons-naturelles' && !$value) {
    //                     $fail(__('product.validation.price_required'));
    //                 }
    //             },
    //         ],
    //         'price_half_litre' => [
    //             'nullable',
    //             'numeric',
    //             'min:0',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 if ($request->category_id && Category::find($request->category_id)->slug === 'boissons-naturelles' && !$value) {
    //                     $fail(__('product.validation.price_half_litre_required'));
    //                 }
    //             },
    //         ],
    //         'price_litre' => [
    //             'nullable',
    //             'numeric',
    //             'min:0',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 if ($request->category_id && Category::find($request->category_id)->slug === 'boissons-naturelles' && !$value) {
    //                     $fail(__('product.validation.price_litre_required'));
    //                 }
    //             },
    //         ],
    //     ]);


    //     // Gestion de l'upload de l'image
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('products', 'public');
    //     }

    //     // Création du produit
    //     Product::create([
    //         'name' => $request->name,
    //         'description' => $request->description,
    //         'price' => $request->price,
    //         'image' => isset($imagePath) ? $imagePath : null,
    //         'status' => $request->status,
    //         'price_half_litre' => $request->price_half_litre,
    //         'price_litre' => $request->price_litre,
    //         'category_id' => $request->category_id,
    //     ]);

    //     return redirect()->route('admin.products.index')->with('success', 'Produit ajouté avec succès.');
    // }
    public function store(Request $request)
    {
        // Récupération de la langue actuelle
        $locale = app()->getLocale();

        // Validation des champs
        $validated = $request->validate([
            'price_choice' => 'required|in:normal,detailed',
            'name' => 'required|array', // Nom en plusieurs langues
            'name.*' => 'required|string|max:255',
            'description' => 'nullable|array', // Description en plusieurs langues
            'description.*' => 'nullable|string',
            'category_id' => 'required|exists:categories,id', // On garde category_id
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status' => ['required', 'string', Rule::in(array_keys(Product::getStatusLabels()))],

       'price' => [
            'nullable', 'numeric', 'min:0',
            function ($attribute, $value, $fail) use ($request) {
                if ($request->input('price_choice') === 'normal' && (!$value)) {
                    $fail('Le prix normal est requis lorsque vous choisissez "Prix normal".');
                }
                if ($request->input('price_choice') === 'detailed' && $value) {
                    $fail('Vous ne pouvez pas renseigner "price" en même temps que "price_half_litre" et "price_litre".');
                }
            }
        ],
        'price_half_litre' => [
            'nullable', 'numeric', 'min:0',
            function ($attribute, $value, $fail) use ($request) {
                if ($request->input('price_choice') === 'detailed' && (!$value && !$request->input('price_litre'))) {
                    $fail('Au moins un des prix détaillés doit être renseigné.');
                }
            }
        ],
        'price_litre' => [
            'nullable', 'numeric', 'min:0',
            function ($attribute, $value, $fail) use ($request) {
                if ($request->input('price_choice') === 'detailed' && (!$value && !$request->input('price_half_litre'))) {
                    $fail('Au moins un des prix détaillés doit être renseigné.');
                }
            }
        ],
            // Prix dynamique basé sur le slug de la catégorie dans la langue actuelle
            // 'price' => [
            //     'nullable',
            //     'numeric',
            //     'min:0',
            //     function ($attribute, $value, $fail) use ($request, $locale) {
            //         $category = Category::find($request->category_id);
            //         if ($category) {
            //             $slug = $category->getTranslation('slug', $locale); // Récupération du slug en fonction de la langue
            //             if ($slug === 'boissons-naturelles' && !$value) {
            //                 $fail(__('product.validation.price_required'));
            //             }
            //         }
            //     },
            // ],
            // 'price_half_litre' => [
            //     'nullable',
            //     'numeric',
            //     'min:0',
            //     function ($attribute, $value, $fail) use ($request, $locale) {
            //         $category = Category::find($request->category_id);
            //         if ($category) {
            //             $slug = $category->getTranslation('slug', $locale);
            //             if ($slug === 'boissons-naturelles' && !$value) {
            //                 $fail(__('product.validation.price_half_litre_required'));
            //             }
            //         }
            //     },
            // ],
            // 'price_litre' => [
            //     'nullable',
            //     'numeric',
            //     'min:0',
            //     function ($attribute, $value, $fail) use ($request, $locale) {
            //         $category = Category::find($request->category_id);
            //         if ($category) {
            //             $slug = $category->getTranslation('slug', $locale);
            //             if ($slug === 'boissons-naturelles' && !$value) {
            //                 $fail(__('product.validation.price_litre_required'));
            //             }
            //         }
            //     },
            // ],
        ]);

        // Gestion de l'upload de l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Création du produit avec traduction
        $product = new Product();
        $product->setTranslations('name', $validated['name']);
        $product->setTranslations('description', $validated['description'] ?? []);
        // $product->price = $validated['price'];
        $product->image = $imagePath;
        $product->status = $validated['status'];

        // $product->price_half_litre = $validated['price_half_litre'];
        // $product->price_litre = $validated['price_litre'];
        $product->category_id = $validated['category_id'];

        $product->price_choice = $validated['price_choice'];
        $product->price = $validated['price_choice'] === 'normal' ? $validated['price'] : null;
        $product->price_half_litre = $validated['price_choice'] === 'detailed' ? $validated['price_half_litre'] : null;
        $product->price_litre = $validated['price_choice'] === 'detailed' ? $validated['price_litre'] : null;
        $product->save();

        return redirect()->route('admin.products.index')->with('success', __('product.success.created'));
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
    // public function edit(Product $product)
    // {


    //       // Définir les mots-clés associés à la catégorie "Livraisons"
    //       $livraisonKeywords = ['Livraison', 'Livraisons'];

    //       $boissonKeywords = ['Boisson', 'Naturelles', 'Fruit', 'Fruits'];

    //        // Récupérer dynamiquement les slugs des catégories correspondant à "Boissons"
    //        $slugsBoissons = Category::where(function ($query) use ($boissonKeywords) {
    //           foreach ($boissonKeywords as $keyword) {
    //               $query->orWhere('name', 'LIKE', "%$keyword%");
    //           }
    //       })
    //       ->pluck('slug')
    //       ->toArray();

    //       // Récupérer dynamiquement les slugs des catégories correspondant aux mots-clés
    //       $slugsLivraison = Category::where(function ($query) use ($livraisonKeywords) {
    //           foreach ($livraisonKeywords as $keyword) {
    //               $query->orWhere('name', 'LIKE', "%$keyword%");
    //           }
    //       })
    //       ->pluck('slug')
    //       ->toArray();

    //       // Récupérer toutes les catégories sauf celles associées aux "Livraisons"
    //       $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

    //       return view('admin.products.edit', compact('product', 'categories','slugsBoissons'));

    // }

    public function edit(Product $product)
    {
        // Vérification des permissions


        // Récupération de la langue actuelle
        $locale = app()->getLocale();

        // Récupérer les slugs liés aux "livraisons"
        $slugsLivraison = Category::select('slug')
            ->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%livraison%'])
            ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%delivery%'])
            ->get()
            ->map(function ($category) use ($locale) {
                $decodedSlug = json_decode($category->slug, true);
                return $decodedSlug[$locale] ?? null;
            })
            ->filter()
            ->toArray();

        // Récupérer les catégories excluant celles liées à la livraison
        $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

        // Récupérer les slugs « Boissons »
        $slugsBoissons = Category::select('slug')
            ->where(function ($query) use ($locale) {
                $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%boisson%'])
                      ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"$locale\"'))) LIKE ?", ['%drink%']);
            })
            ->get()
            ->map(function ($category) use ($locale) {
                $decodedSlug = json_decode($category->slug, true);
                return $decodedSlug[$locale] ?? null;
            })
            ->filter()
            ->toArray();

        // Retourne la vue d’édition, en passant le produit, les catégories et éventuellement slugsBoissons
        return view('admin.products.edit', compact('product', 'categories', 'slugsBoissons'));
    }


    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Product $product)
    // {
    //     // Validation des données avec des règles conditionnelles
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'category_id' => 'required|exists:categories,id',
    //         'price' => [
    //             'nullable',
    //             'numeric',
    //             'min:0',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 // Vérifier si la catégorie sélectionnée nécessite des prix spécifiques
    //                 $category = Category::find($request->category_id);
    //                 if ($category && $category->slug !== 'boissons-naturelles' && !$value) {
    //                     $fail(__('product.validation.price_required'));
    //                 }
    //             },
    //         ],
    //         'price_half_litre' => [
    //             'nullable',
    //             'numeric',
    //             'min:0',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 $category = Category::find($request->category_id);
    //                 if ($category && $category->slug === 'boissons-naturelles' && !$value) {
    //                     $fail(__('product.validation.price_half_litre_required'));
    //                 }
    //             },
    //         ],
    //         'price_litre' => [
    //             'nullable',
    //             'numeric',
    //             'min:0',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 $category = Category::find($request->category_id);
    //                 if ($category && $category->slug === 'boissons-naturelles' && !$value) {
    //                     $fail(__('product.validation.price_litre_required'));
    //                 }
    //             },
    //         ],
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Validation de l'image
    //         'status' => 'required|string|in:available,recommended,seasonal',
    //     ]);

    //     // Mise à jour des champs du produit
    //     $product->name = $validatedData['name'];
    //     $product->description = $validatedData['description'];
    //     $product->category_id = $validatedData['category_id'];
    //     $product->price = $validatedData['price'];
    //     $product->price_half_litre = $validatedData['price_half_litre'];
    //     $product->price_litre = $validatedData['price_litre'];
    //     $product->status = $validatedData['status'];

    //     // Gestion de l'image (si une nouvelle image est téléchargée)
    //     if ($request->hasFile('image')) {
    //         // Supprimer l'ancienne image si elle existe
    //         if ($product->image) {
    //             Storage::disk('public')->delete($product->image);
    //         }

    //         // Sauvegarder la nouvelle image
    //         $product->image = $request->file('image')->store('products', 'public');
    //     }

    //     // Sauvegarder les modifications dans la base de données
    //     $product->save();

    //     // Redirection avec un message de succès
    //     return redirect()->route('admin.products.index')->with('success', __('product.success.updated'));
    // }

    public function update(Request $request, Product $product)
    {
        // Récupération de la langue actuelle
        $locale = app()->getLocale();

        // Validation des données avec des règles conditionnelles
        $validatedData = $request->validate([
            'price_choice' => 'required|in:normal,detailed',
            'name' => 'required|array', // Champs traduisibles sous forme de tableau
            'name.*' => 'required|string|max:255',
            'description' => 'nullable|array', // Description traduisible
            'description.*' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',

    'price' => [
            'nullable', 'numeric', 'min:0',
            function ($attribute, $value, $fail) use ($request) {
                if ($request->input('price_choice') === 'normal' && (!$value)) {
                    $fail('Le prix normal est requis lorsque vous choisissez "Prix normal".');
                }
                if ($request->input('price_choice') === 'detailed' && $value) {
                    $fail('Vous ne pouvez pas renseigner "price" en même temps que "price_half_litre" et "price_litre".');
                }
            }
        ],
        'price_half_litre' => [
            'nullable', 'numeric', 'min:0',
            function ($attribute, $value, $fail) use ($request) {
                if ($request->input('price_choice') === 'detailed' && (!$value && !$request->input('price_litre'))) {
                    $fail('Au moins un des prix détaillés doit être renseigné.');
                }
            }
        ],
        'price_litre' => [
            'nullable', 'numeric', 'min:0',
            function ($attribute, $value, $fail) use ($request) {
                if ($request->input('price_choice') === 'detailed' && (!$value && !$request->input('price_half_litre'))) {
                    $fail('Au moins un des prix détaillés doit être renseigné.');
                }
            }
        ],
            // 'price' => [
            //     'nullable',
            //     'numeric',
            //     'min:0',
            //     function ($attribute, $value, $fail) use ($request, $locale) {
            //         $category = Category::find($request->category_id);
            //         if ($category) {
            //             $slug = $category->getTranslation('slug', $locale);
            //             if ($slug !== 'boissons-naturelles' && !$value) {
            //                 $fail(__('product.validation.price_required'));
            //             }
            //         }
            //     },
            // ],
            // 'price_half_litre' => [
            //     'nullable',
            //     'numeric',
            //     'min:0',
            //     function ($attribute, $value, $fail) use ($request, $locale) {
            //         $category = Category::find($request->category_id);
            //         if ($category) {
            //             $slug = $category->getTranslation('slug', $locale);
            //             if ($slug === 'boissons-naturelles' && !$value) {
            //                 $fail(__('product.validation.price_half_litre_required'));
            //             }
            //         }
            //     },
            // ],
            // 'price_litre' => [
            //     'nullable',
            //     'numeric',
            //     'min:0',
            //     function ($attribute, $value, $fail) use ($request, $locale) {
            //         $category = Category::find($request->category_id);
            //         if ($category) {
            //             $slug = $category->getTranslation('slug', $locale);
            //             if ($slug === 'boissons-naturelles' && !$value) {
            //                 $fail(__('product.validation.price_litre_required'));
            //             }
            //         }
            //     },
            // ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Validation de l'image
            'status' => ['required', 'string', Rule::in(array_keys(Product::getStatusLabels()))],
        ]);

        // Mise à jour des champs traduisibles
        $product->setTranslations('name', $validatedData['name']);
        $product->setTranslations('description', $validatedData['description'] ?? []);

        // Mise à jour des autres champs
        $product->category_id = $validatedData['category_id'];

          // Champ price_choice
        $product->price_choice = $validatedData['price_choice'];
        $product->price = $validatedData['price_choice'] === 'normal' ? $validatedData['price'] : null;
        $product->price_half_litre = $validatedData['price_choice'] === 'detailed' ? $validatedData['price_half_litre'] : null;
        $product->price_litre = $validatedData['price_choice'] === 'detailed' ? $validatedData['price_litre'] : null;

        // $product->price = $validatedData['price'];
        // $product->price_half_litre = $validatedData['price_half_litre'];
        // $product->price_litre = $validatedData['price_litre'];
        $product->status = $validatedData['status'];

        // Gestion de l'image (si une nouvelle image est téléchargée)
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Sauvegarder la nouvelle image
            $product->image = $request->file('image')->store('products', 'public');
        }

        // Sauvegarde en base de données
        $product->save();

        // Redirection avec un message de succès
        return redirect()->route('admin.products.index')->with('success', __('product.success.updated'));
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

        return redirect()->route('admin.products.index')->with('success', __('product.success.deleted'));
        // return redirect()->route('admin.products.index')->with('success', 'Produit supprimé avec succès.');
    }

     /**
    * Voici une explication détaillée sur l'utilisation des différents champs de prix et une simulation des cas liés à vos Plats et Tarifs :

   *  Explications des champs de prix :
    * price

    * Sert à enregistrer le prix de base d'un produit ou d'un plat.
   *  Par exemple :
    * Prix d’un plat seul (ex : Garba) : £10.
    * Prix d’un produit prêt à cuisiner (ex : Gombo bouilli) : £3.
    * menu_price

   *  Utilisé pour les menus combinés (plat + boisson + fruit).
    * Par exemple :
   *  Menu complet (ex : Garba + Jus de bissap + fruit) : £15.
    * extra_price

    * Sert à enregistrer le coût des accompagnements supplémentaires.
    * Par exemple :
    * Ajout d’une portion supplémentaire de poisson, alloco, ou attiéké : £2.
    * half_litre_price

    * Prix pour une boisson naturelle au demi-litre.
    * Par exemple :
    * Jus de bissap (500 ml) : £1.
    * litre_price

    * Prix pour une boisson naturelle au litre.
    * Par exemple :
    * Jus de bissap (1 L) : £2.
    * Simulation des différents cas pour vos Plats et Tarifs :
    * Plat seul

    * Produit : Garba (attiéké + poisson thon frit)
   *  Prix (price) : £10
    * Total : £10
    * Menu complet (plat + boisson + fruit)

   *  Produit : Poulet braisé avec attiéké, alloco, ou frites
    * Boisson : Jus de tamarin (1 litre)
    * Fruit : Une portion de fruit (inclus dans le menu)
    * Prix (menu_price) : £15
    * Total : £15
   *  Plat seul avec accompagnement supplémentaire

    * Produit : Placali Sauce Graine (avec poisson fumé)
    * Accompagnement supplémentaire : Portion d’alloco
    * Prix (price) : £10
   *  Extra (extra_price) : £2
   *  Total : £12
   *  Boissons naturelles

   *  Produit : Jus de gingembre
   *  Format : Demi-litre
   *  Prix (half_litre_price) : £1
   *  Total : £1
   *  Format : Litre
    * Prix (litre_price) : £2
   *  Total : £2
   *  Produits prêts à cuisiner

   *  Produit : Aubergines cuites
   *  Prix (price) : £4
   *  Total : £4
 **/

}





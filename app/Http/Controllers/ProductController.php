<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Pour gérer la requête HTTP
use App\Models\Product; // Pour manipuler le modèle Product
use Illuminate\Support\Facades\Storage; // Pour gérer le stockage de fichiers (images)
use Illuminate\Support\Facades\Validator; // Si tu utilises la validation manuelle
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Nettoyer l'entrée utilisateur pour éviter les espaces inutiles
        $search = trim($request->get('search'));

        // Définir les mots-clés associés à la catégorie "Livraisons"
        $livraisonKeywords = ['Livraison', 'Livraisons'];

        $boissonKeywords = ['Boisson', 'Naturelles', 'Fruit', 'Fruits'];

         // Récupérer dynamiquement les slugs des catégories correspondant à "Boissons"
         $slugsBoissons = Category::where(function ($query) use ($boissonKeywords) {
            foreach ($boissonKeywords as $keyword) {
                $query->orWhere('name', 'LIKE', "%$keyword%");
            }
        })
        ->pluck('slug')
        ->toArray();

        // Récupérer dynamiquement les slugs des catégories correspondant aux mots-clés
        $slugsLivraison = Category::where(function ($query) use ($livraisonKeywords) {
            foreach ($livraisonKeywords as $keyword) {
                $query->orWhere('name', 'LIKE', "%$keyword%");
            }
        })
        ->pluck('slug')
        ->toArray();

        // Appliquer les filtres de recherche
        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                             ->orWhere('description', 'like', '%' . $search . '%')
                             ->orWhere('status', 'like', '%' . $search . '%');
                })
                ->orWhereHas('category', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
            })
            // Exclure les catégories associées aux slugs des "Livraisons"
            ->whereHas('category', function ($query) use ($slugsLivraison) {
                $query->whereNotIn('slug', $slugsLivraison);
            })
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Récupérer toutes les catégories sauf celles associées aux "Livraisons"
        $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

        // Retourner les données à la vue
        return view('admin.products.index', compact('products', 'categories','slugsBoissons'));
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
        // Validation des champs
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status' => 'required|string|in:available,recommended,seasonal',
            'price' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->category_id && Category::find($request->category_id)->slug === 'boissons-naturelles' && !$value) {
                        $fail('Le prix normal est requis pour les catégories autres que "Boissons naturelles".');
                    }
                },
            ],
            'price_half_litre' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->category_id && Category::find($request->category_id)->slug === 'boissons-naturelles' && !$value) {
                        $fail('Le prix pour 1/2 litre ou 1 tasse est requis pour les "Boissons naturelles".');
                    }
                },
            ],
            'price_litre' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->category_id && Category::find($request->category_id)->slug === 'boissons-naturelles' && !$value) {
                        $fail('Le prix pour 1 litre ou 1 paquet est requis pour les "Boissons naturelles".');
                    }
                },
            ],
        ]);


        // Gestion de l'upload de l'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Création du produit
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => isset($imagePath) ? $imagePath : null,
            'status' => $request->status,
            'price_half_litre' => $request->price_half_litre,
            'price_litre' => $request->price_litre,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Produit ajouté avec succès.');
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
    public function edit(Product $product)
    {


          // Définir les mots-clés associés à la catégorie "Livraisons"
          $livraisonKeywords = ['Livraison', 'Livraisons'];

          $boissonKeywords = ['Boisson', 'Naturelles', 'Fruit', 'Fruits'];

           // Récupérer dynamiquement les slugs des catégories correspondant à "Boissons"
           $slugsBoissons = Category::where(function ($query) use ($boissonKeywords) {
              foreach ($boissonKeywords as $keyword) {
                  $query->orWhere('name', 'LIKE', "%$keyword%");
              }
          })
          ->pluck('slug')
          ->toArray();

          // Récupérer dynamiquement les slugs des catégories correspondant aux mots-clés
          $slugsLivraison = Category::where(function ($query) use ($livraisonKeywords) {
              foreach ($livraisonKeywords as $keyword) {
                  $query->orWhere('name', 'LIKE', "%$keyword%");
              }
          })
          ->pluck('slug')
          ->toArray();

          // Récupérer toutes les catégories sauf celles associées aux "Livraisons"
          $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

          return view('admin.products.edit', compact('product', 'categories','slugsBoissons'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Validation des données avec des règles conditionnelles
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    // Vérifier si la catégorie sélectionnée nécessite des prix spécifiques
                    $category = Category::find($request->category_id);
                    if ($category && $category->slug !== 'boissons-naturelles' && !$value) {
                        $fail('Le prix normal est requis pour les catégories autres que "Boissons naturelles".');
                    }
                },
            ],
            'price_half_litre' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $category = Category::find($request->category_id);
                    if ($category && $category->slug === 'boissons-naturelles' && !$value) {
                        $fail('Le prix pour 1/2 litre ou 1 tasse est requis pour les "Boissons naturelles".');
                    }
                },
            ],
            'price_litre' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $category = Category::find($request->category_id);
                    if ($category && $category->slug === 'boissons-naturelles' && !$value) {
                        $fail('Le prix pour 1 litre ou 1 paquet est requis pour les "Boissons naturelles".');
                    }
                },
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Validation de l'image
            'status' => 'required|string|in:available,recommended,seasonal',
        ]);

        // Mise à jour des champs du produit
        $product->name = $validatedData['name'];
        $product->description = $validatedData['description'];
        $product->category_id = $validatedData['category_id'];
        $product->price = $validatedData['price'];
        $product->price_half_litre = $validatedData['price_half_litre'];
        $product->price_litre = $validatedData['price_litre'];
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

        // Sauvegarder les modifications dans la base de données
        $product->save();

        // Redirection avec un message de succès
        return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour avec succès!');
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

        return redirect()->route('admin.products.index')->with('success', 'Produit supprimé avec succès.');
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





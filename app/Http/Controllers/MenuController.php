<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Pour manipuler le modèle Product
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Zone;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Shipping;


class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur

        $livraisonKeywords = ['Livraison', 'Livraisons'];

        // Récupérer dynamiquement les slugs des catégories correspondant aux mots-clés
        $slugsLivraison = Category::where(function ($query) use ($livraisonKeywords) {
            foreach ($livraisonKeywords as $keyword) {
                $query->orWhere('name', 'LIKE', "%$keyword%");
            }
        })
        ->pluck('slug')
        ->toArray();

        // Appliquer la recherche sur les produits (menus)
        $menus = Product::query()
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
        ->paginate(6);

        // Récupérer toutes les catégories sauf celles associées aux "Livraisons"
        $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

        // Charger le panier depuis la session
        $cart = Session::get('cart', []); // Par défaut, le panier est vide s'il n'existe pas dans la session

        // Calculer le sous-total du panier
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity']; // Sous-total = prix x quantité
        });

        return view('menus.index', compact('menus', 'cart', 'subtotal', 'categories'));
    }

    public function cartCount()
    {
        $cart = Session::get('cart', []);
        $count = collect($cart)->sum('quantity');

        return response()->json([
            'count' => $count,
        ]);
    }

    // public function addToCart(Request $request)
    // {

    //     $product = Product::findOrFail($request->product_id);

    //     // Obtenir le panier existant ou en créer un nouveau
    //     $cart = Session::get('cart', []);

    //     // Vérifier si le produit est déjà dans le panier
    //     if (isset($cart[$product->id])) {
    //         $cart[$product->id]['quantity']++;
    //     } else {
    //         // Ajouter un nouveau produit
    //         $cart[$product->id] = [
    //             'name' => $product->name,
    //             'price' => $product->price,
    //             'quantity' => 1,
    //             'image' => $product->image, // Si vous avez un champ image
    //         ];
    //     }

    //     // Sauvegarder le panier dans la session
    //     Session::put('cart', $cart);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Produit ajouté au panier.',
    //         'cart' => $cart,
    //     ]);
    // }


    public function addToCart(Request $request)
    {


        $customerId = Auth::guard('customer')->id();

        // Vérifier si l'utilisateur est connecté
        if (!$customerId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Veuillez vous connecter pour passer une commande.'
            ], 401); // Retourner une erreur 401 Unauthorized
        }

        $product = Product::findOrFail($request->product_id);

        // Vérifier si une taille a été fournie pour les boissons naturelles
        $size = null;
        $price = $product->price;

        if ($product->category->slug === 'boissons-naturelles') {
            $size = $request->input('size', 'half_litre'); // Valeur par défaut : demi-litre
            if ($size === 'litre') {
                $price = $product->price_litre;
            } else {
                $price = $product->price_half_litre;
            }
        }

        // Obtenir ou initialiser le panier
        $cart = Session::get('cart', []);

        // Ajouter ou mettre à jour le produit dans le panier
        $cartKey = $product->id . ($size ? "-$size" : '');
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                'name' => $product->name,
                'price' => $price,
                'quantity' => 1,
                'image' => $product->image,
                'size' => $size,
            ];
        }

        // Sauvegarder le panier
        Session::put('cart', $cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Produit ajouté au panier.',
            'cart' => $cart,
        ]);
    }


    // CartController
    public function viewCart()
    {
        $cart = session('cart', []);
        $subtotal = 0;

        // Mettre à jour chaque élément du panier avec la clé 'subtotal' et calculer le sous-total total
        foreach ($cart as $id => &$item) { // Utilisation de référence pour modifier directement les éléments du panier
            $item['subtotal'] = $item['price'] * $item['quantity'];
            $subtotal += $item['subtotal'];
        }

        // Sauvegarder le panier mis à jour dans la session
        session(['cart' => $cart]);

        // Calculer le total (par exemple, avec des taxes ou autres frais)
        $total = $subtotal; // Exemple, ajouter des frais si nécessaire

        return view('menus.cartView', compact('cart', 'subtotal', 'total'));
    }

    public function removeFromCart(Request $request)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            Session::put('cart', $cart);
        }

        // Recalculer le sous-total
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Retourner une réponse JSON avec le message de succès et les nouvelles données du panier
        return response()->json([
            'status' => 'success',
            'message' => 'Product removed from cart',
            'cart_count' => count($cart),
            'subtotal' => $subtotal, // Nouveau sous-total
            'cart' => $cart, // Panier mis à jour
        ]);
    }

    public function viewCheckout()
    {
        $zones = Zone::all();
        $payments = Payment::all();

        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
        // $total = $subtotal;

        // Gestion des frais de livraison
        $standardShipping = Shipping::where('name', 'Standard')->first();
        $freeShipping = Shipping::where('name', 'Gratuit')->first();

        if ($subtotal >= 30) {
            $shipping_cost = 0; // Livraison gratuite
        } else {
            $shipping_cost = $standardShipping ? $standardShipping->price : 0; // Livraison standard
        }

        $total = $subtotal + $shipping_cost;


        return view('menus.checkoutView', compact('cart', 'subtotal', 'total','zones','payments','shipping_cost'));
    }

    public function storeOrder(Request $request){
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country_code' => 'required|string|max:255', // Validation pour le code pays
            'zip' => 'required|string|max:10',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'order_notes' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'zone_id' => 'required|exists:zones,id',
            'payment_id' => 'required|exists:payments,id',
            'terms' => 'accepted',
        ]);

        $customerId = Auth::guard('customer')->id();
        if ( !$customerId ) {
            return redirect()->route('customer.login')->withErrors(['error' => 'Veuillez vous connecter pour passer une commande.']);
        }


        // Vérification du coupon s'il est fourni
        $coupon = null;
        $discount = 0;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();

            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->discount; // Appliquer la remise
            } else {
                return back()->withErrors(['coupon_code' => 'Le coupon est invalide ou expiré.']);
            }
        }

        // Calculer le total après la remise
        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $total = $subtotal - ($subtotal * ($discount / 100));


        // Gestion des frais de livraison
        $standardShipping = Shipping::where('name', 'Standard')->first();
        $freeShipping = Shipping::where('name', 'Gratuit')->first();

        if ($total >= 30) {
            $shipping = $freeShipping;
            $shippingCost = 0;
        } else {
            $shipping = $standardShipping;
            $shippingCost = $shipping->price;
        }

        $total += $shippingCost;

        // Créer la commande
        $order = Order::create([
            'code' => Order::generateOrderCode(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'zone_id' => $request->zone_id,
            'payment_id' => $request->payment_id,
            'terms' => $request->terms,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country_code' => $request->country_code,
            'zip' => $request->zip,
            'shipping_cost' => $shippingCost,
            'total' =>  $total, // Fonction pour calculer le total
            'coupon_id' => $coupon ? $coupon->id : null,  // Enregistrer l'ID du coupon
            'order_notes' => $request->input('order_notes', null),
            'customer_id' => $customerId,
            'status' => 'pending', // statut initial
        ]);



        // Associer les produits à la commande
        $cart = session()->get('cart', []); // Récupérer le panier de la session

        if (!empty($cart)) {
            foreach ($cart as $key => $item) {
                // Extraire uniquement l'ID du produit à partir de la clé
                $productId = explode('-', $key)[0]; // Récupère la partie avant le tiret

                // Vérification que le produit existe avant l'association
                $product = Product::find($productId);

                if ($product) {
                    $order->products()->attach($productId, [
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'size' => $item['size'], // Ajouter la taille si nécessaire
                    ]);
                }
            }
        }

        // Vider le panier
        session()->forget('cart');

        return redirect()->route('menus.index')->with('success', 'Commande passée avec succès.');
    }

    private function calculateTotal(Request $request, $discount)
    {
        // Récupérer le panier de la session
        $cart = session('cart', []);

        // Calcul du sous-total
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Appliquer la remise si elle existe
        if ($discount > 0) {
            return $subtotal - ($subtotal * ($discount / 100));
        }

        return $subtotal;
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

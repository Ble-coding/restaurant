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
use Illuminate\Support\Facades\Http;
use App\Models\PaymentGateway;
use App\Models\Category;
use App\Models\Shipping;
use App\Services\Cinetpay;
use App\Services\Stripe;
use Illuminate\Support\Facades\Log; // Ajoutez cette ligne
use Exception;

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
            // Sauvegarder l'URL actuelle dans la session
            session()->put('intended_url', url()->previous());

            return response()->json([
                'status' => 'error',
                'message' => 'Veuillez vous connecter pour passer une commande.',
                'redirect' => route('customer.login') // URL de connexion
            ], 401);
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


    public function storeOrder(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country_code' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'order_notes' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id',
            'zone_id' => 'required|exists:zones,id',
            'payment_id' => 'required|exists:payments,id',
            'terms' => 'accepted',
        ]);

        $customerId = Auth::guard('customer')->id();
        if (!$customerId) {
            return redirect()->route('customer.login')->withErrors(['error' => 'Veuillez vous connecter pour passer une commande.']);
        }

        $coupon = null;
        $discount = 0;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->discount;
            } else {
                return back()->withErrors(['coupon_code' => 'Le coupon est invalide ou expiré.']);
            }
        }

        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $total = $subtotal - ($subtotal * ($discount / 100));

        $shipping = Shipping::where('name', $total >= 30 ? 'Gratuit' : 'Standard')->first();
        $shippingCost = $total >= 30 ? 0 : $shipping->price;
        $total += $shippingCost;

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
            'total' => $total,
            'coupon_id' => $coupon ? $coupon->id : null,
            'order_notes' => $request->input('order_notes', null),
            'customer_id' => $customerId,
            'status' => 'pending',
        ]);

        foreach ($cart as $key => $item) {
            $productId = explode('-', $key)[0];
            $product = Product::find($productId);
            if ($product) {
                $order->products()->attach($productId, [
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'size' => $item['size'] ?? null,
                ]);
            }
        }

        session()->forget('cart');



        if ($order->payment->name === "Cinetpay") {
            $paymentGateway = PaymentGateway::whereHas('payment', function ($query) {
                $query->where('name', 'Cinetpay');
            })->first();


            if (!$paymentGateway) {
                return back()->withErrors(['error' => 'Passerelle de paiement non configurée.']);
            }

            // Conversion GBP -> XOF
            $conversionRate = $this->getConversionRate('GBP', 'XOF');
            if ($conversionRate <= 0) {
                return back()->withErrors(['error' => 'Impossible de récupérer le taux de conversion.']);
            }

            // Calcul du montant total en XOF (entier)
            $totalXOF = (int) round($total * $conversionRate * 100); // Multiplier par 100 pour les centimes et convertir en entier


            try {
                $cinetPay = new CinetPay(
                    $paymentGateway->site_id,
                    $paymentGateway->api_key,
                    'v2' // Vous pouvez adapter la version si nécessaire
                );

                $cinetPay->setTransaction(
                    $order->code,
                    $totalXOF,
                    $order->email,
                    $order->phone,
                    "Paiement de la commande {$order->code}",
                    $order->first_name,
                    $order->last_name
                );


                // Définir les URL dynamiquement à partir de la configuration de votre passerelle
                $cinetPay->transaction['notify_url'] = $paymentGateway->callback_url; // URL pour recevoir les notifications
                $cinetPay->transaction['return_url'] = route('customer.orders.success'); // URL de redirection après paiement

                $paymentUrl = $cinetPay->getPaymentUrl();

                return redirect()->away($paymentUrl);

            } catch (Exception $e) {
                \Log::error("CinetPay Payment Error", ['error' => $e->getMessage()]);
                return back()->withErrors(['error' => 'Erreur de paiement : ' . $e->getMessage()]);
            }


        }else if ($order->payment->name === "Stripe") {
            \Log::info("Début du traitement Stripe pour la commande", ['order_code' => $order->code]);

            // Vérifiez si la passerelle de paiement Stripe est configurée
            $paymentGateway = PaymentGateway::whereHas('payment', function ($query) {
                $query->where('name', 'Stripe');
            })->first();


            if (!$paymentGateway) {
                \Log::error("Passerelle de paiement Stripe non configurée.");
                return back()->withErrors(['error' => 'Passerelle de paiement Stripe non configurée.']);
            }

            // Optionnel : Conversion de devise si nécessaire (GBP -> USD, par exemple)
            \Log::info("Tentative de récupération du taux de conversion pour Stripe", ['from' => 'GBP', 'to' => 'USD']);
            $conversionRate = $this->getConversionRate('GBP', 'USD');

            if ($conversionRate <= 0) {
                \Log::error("Taux de conversion invalide ou non disponible pour Stripe", ['conversionRate' => $conversionRate]);
                return back()->withErrors(['error' => 'Impossible de récupérer le taux de conversion pour Stripe.']);
            }

            // Calculer le montant total en centimes (Stripe requiert des montants en centimes)
            $totalUSD = (int) round($total * $conversionRate * 100);
            \Log::info("Montant total calculé pour Stripe", ['totalUSD' => $totalUSD]);

            try {
                // Initialisation de Stripe avec les clés API
                \Log::info("Initialisation de Stripe", ['api_key' => $paymentGateway->api_key]);

                $stripe = new Stripe(
                    $paymentGateway->api_key,
                    $paymentGateway->secret_key
                );

                // Création de l'intention de paiement Stripe
                \Log::info("Création de l'intention de paiement Stripe", [
                    'amount' => $totalUSD,
                    'currency' => 'usd',
                    'order_code' => $order->code,
                ]);

                $paymentIntent = $stripe->initiatePayment([
                    'amount' => $totalUSD,
                    'currency' => 'usd', // Assurez-vous que la devise correspond à votre paramétrage Stripe
                    'description' => "Paiement pour la commande {$order->code}",
                    'metadata' => [
                        'order_code' => $order->code,
                        'email' => $order->email,
                        'phone' => $order->phone,
                    ],
                ]);

                // Vérifiez si l'intention de paiement contient une URL de redirection
                $paymentUrl = $paymentIntent['next_action']['redirect_to_url']['url'] ?? null;

                if (!$paymentUrl) {
                    \Log::error("URL de redirection Stripe non disponible", ['paymentIntent' => $paymentIntent]);
                    return back()->withErrors(['error' => 'Erreur : URL de redirection Stripe non disponible.']);
                }

                // Mettre à jour le statut de la commande
                \Log::info("Mise à jour de la commande avec l'intention de paiement Stripe", [
                    'order_code' => $order->code,
                    'payment_intent_id' => $paymentIntent['id'],
                ]);

                $order->update([
                    'status' => 'pending_payment', // Exemple de statut
                    'payment_intent_id' => $paymentIntent['id'], // Enregistrez l'identifiant Stripe pour suivi
                ]);

                // Redirigez l'utilisateur vers l'URL de paiement Stripe
                \Log::info("Redirection vers Stripe", ['paymentUrl' => $paymentUrl]);
                return redirect()->away($paymentUrl);

            } catch (\Exception $e) {
                \Log::error("Erreur Stripe lors du paiement", [
                    'order_code' => $order->code,
                    'error' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString(),
                ]);

                // Retournez une erreur à l'utilisateur
                return back()->withErrors(['error' => 'Erreur de paiement Stripe : ' . $e->getMessage()]);
            }
        }
    }

    public function getConversionRate($fromCurrency, $toCurrency)
    {
        try {
            // Appel à une API de conversion monétaire
            $response = Http::get("https://api.exchangerate-api.com/v4/latest/{$fromCurrency}");

            if ($response->successful() && isset($response['rates'][$toCurrency])) {
                return $response['rates'][$toCurrency];
            }
            return 0; // Taux invalide ou non disponible
        } catch (Exception $e) {
            \Log::error("Erreur lors de la récupération du taux de conversion : " . $e->getMessage());
            return 0; // Retourner un taux invalide en cas d'échec
        }
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


    public function paymentSuccess()
    {
        return redirect()->route('customer.orders.index')
            ->with('success', 'Paiement Réussi ! Votre commande a été confirmée avec succès. Merci pour votre achat');
    }

        public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string',
        ]);

        try {
            // Récupérer les informations de configuration Stripe
            $paymentGateway = PaymentGateway::where('name', 'Stripe')->firstOrFail();

            $stripe = new Stripe($paymentGateway->api_key, $paymentGateway->secret_key);
            $paymentIntent = $stripe->initiatePayment([
                'amount' => $request->amount,
                'currency' => $request->currency,
                'description' => 'Commande #12345',
            ]);

            return response()->json([
                'payment_intent_id' => $paymentIntent['id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de l’intention de paiement : ' . $e->getMessage(),
            ], 500);
        }
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

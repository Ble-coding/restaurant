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
    protected $stripeService;

    public function __construct(Stripe $stripeService)
    {
        // Injection du service Stripe dans le contrôleur
        $this->stripeService = $stripeService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur
        $status = $request->get('status');
        $categoryId = $request->get('category_id');
        $price = $request->get('price');
        $locale = app()->getLocale();

        $slugsLivraison = Category::select('slug')
        ->where(function ($query) use ($locale) {
            $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"'))) LIKE ?", ['%livraison%'])
                ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"'))) LIKE ?", ['%delivery%']);
        })
        ->get()
        ->map(function ($category) use ($locale) {
            $decodedSlug = json_decode($category->slug, true); // Décoder le JSON
            return $decodedSlug[$locale] ?? null; // Récupérer la valeur correspondant à la langue actuelle
        })
        ->filter()
        ->toArray();


        $menus = Product::query()
            ->when($search, function ($query) use ($search, $locale) {
                $query->where(function ($subQuery) use ($search, $locale) {
                    $subQuery->whereRaw("JSON_EXTRACT(name, '$.$locale') LIKE ?", ["%$search%"])
                             ->orWhereRaw("JSON_EXTRACT(description, '$.$locale') LIKE ?", ["%$search%"]);
                });
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

        $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

        $cart = Session::get('cart', []);

        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return view('menus.index', compact('menus', 'cart', 'subtotal', 'categories'));
    }

    // public function index(Request $request)
    // {
    //     $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur
    //     $status = $request->get('status');
    //     $categoryId = $request->get('category_id');
    //     $price = $request->get('price');

    //     $livraisonKeywords = ['Livraison', 'Livraisons'];

    //     // Récupérer dynamiquement les slugs des catégories correspondant aux mots-clés
    //     $slugsLivraison = Category::where(function ($query) use ($livraisonKeywords) {
    //         foreach ($livraisonKeywords as $keyword) {
    //             $query->orWhere('name', 'LIKE', "%$keyword%");
    //         }
    //     })
    //     ->pluck('slug')
    //     ->toArray();

    //     // Appliquer la recherche sur les produits (menus)
    //     $menus = Product::query()
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

    //     // Charger le panier depuis la session
    //     $cart = Session::get('cart', []);

    //     // Calculer le sous-total du panier
    //     $subtotal = collect($cart)->sum(function ($item) {
    //         return $item['price'] * $item['quantity'];
    //     });

    //     return view('menus.index', compact('menus', 'cart', 'subtotal', 'categories'));
    // }

    // public function index(Request $request)
    // {
    //     $search = trim($request->get('search')); // Nettoyer l'entrée utilisateur

    //     $livraisonKeywords = ['Livraison', 'Livraisons'];

    //     // Récupérer dynamiquement les slugs des catégories correspondant aux mots-clés
    //     $slugsLivraison = Category::where(function ($query) use ($livraisonKeywords) {
    //         foreach ($livraisonKeywords as $keyword) {
    //             $query->orWhere('name', 'LIKE', "%$keyword%");
    //         }
    //     })
    //     ->pluck('slug')
    //     ->toArray();

    //     // Appliquer la recherche sur les produits (menus)
    //     $menus = Product::query()
    //     ->when($search, function ($query) use ($search) {
    //         $query->where(function ($subQuery) use ($search) {
    //             $subQuery->where('name', 'like', '%' . $search . '%')
    //                      ->orWhere('description', 'like', '%' . $search . '%')
    //                      ->orWhere('status', 'like', '%' . $search . '%');
    //         })
    //         ->orWhereHas('category', function ($query) use ($search) {
    //             $query->where('name', 'like', '%' . $search . '%');
    //         });
    //     })
    //     // Exclure les catégories associées aux slugs des "Livraisons"
    //     ->whereHas('category', function ($query) use ($slugsLivraison) {
    //         $query->whereNotIn('slug', $slugsLivraison);
    //     })
    //     ->with('category')
    //     ->orderBy('created_at', 'desc')
    //     ->paginate(4);

    //     // Récupérer toutes les catégories sauf celles associées aux "Livraisons"
    //     $categories = Category::whereNotIn('slug', $slugsLivraison)->get();

    //     // Charger le panier depuis la session
    //     $cart = Session::get('cart', []); // Par défaut, le panier est vide s'il n'existe pas dans la session

    //     // Calculer le sous-total du panier
    //     $subtotal = collect($cart)->sum(function ($item) {
    //         return $item['price'] * $item['quantity']; // Sous-total = prix x quantité
    //     });

    //     return view('menus.index', compact('menus', 'cart', 'subtotal', 'categories'));
    // }

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

        if (!$customerId) {
            session()->put('intended_url', url()->previous());

            return response()->json([
                'status' => 'error',
                'message' => 'Veuillez vous connecter pour passer une commande.',
                'redirect' => route('customer.login')
            ], 401);
        }

        $product = Product::findOrFail($request->product_id);
        $size = $request->input('size');

        if ($product->price_choice === 'detailed') {
            if ($size === 'half_litre') {
                $price = $product->price_half_litre;
            } elseif ($size === 'litre') {
                $price = $product->price_litre;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Veuillez sélectionner une taille valide.'
                ], 400);
            }
        } else {
            $price = $product->price;
        }

        $cart = Session::get('cart', []);

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
                'price_choice' => $product->price_choice ?? 'normal'
            ];
        }

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

        foreach ($cart as $id => &$item) {
            $item['subtotal'] = $item['price'] * $item['quantity'];

            // Assurer que tous les items ont 'price_choice'
            $priceChoice = $item['price_choice'] ?? 'normal';
            $item['price_choice'] = $priceChoice;

            if ($priceChoice === 'detailed') {
                $sizeValue = ($item['size'] === 'half_litre') ? 0.5 : 1;
                $item['total_size'] = $sizeValue * $item['quantity'];
            } else {
                $item['total_size'] = null;
            }

            $subtotal += $item['subtotal'];
        }

        session(['cart' => $cart]);

        $total = $subtotal;

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
        // Récupérer la clé Stripe basée sur le payment_id et le nom 'Stripe'
        // $stripeGateway = PaymentGateway::whereHas('payment', function ($query) {
        //     $query->where('name', 'Stripe');
        // })->first();

        $stripeGateway = PaymentGateway::whereHas('payment', function ($query) {
            $query->where('name', 'Stripe');
        })->first();


        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Gestion des frais de livraison
        $standardShipping = Shipping::where('name', 'Standard')->first();
        $freeShipping = Shipping::where('name', 'Gratuit')->first();

        $shipping_cost = ($subtotal >= 30)
            ? 0 // Livraison gratuite
            : ($standardShipping ? $standardShipping->price : 0); // Livraison standard

        $total = $subtotal + $shipping_cost;

        // Passez les variables nécessaires à la vue
        return view('menus.checkoutView', compact(
            'cart',
            'subtotal',
            'total',
            'zones',
            'payments',
            'shipping_cost',
            'stripeGateway'
        ));
    }



    public function storeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'nullable|string', // Champ Stripe
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

         // Vérifiez si Stripe est sélectionné et que payment_method est requis
            $payment = Payment::findOrFail($request->payment_id);
            if ($payment->name === "Stripe" && !$request->filled('payment_method')) {
                return back()->withErrors(['payment_method' => 'Veuillez sélectionner un moyen de paiement valide pour Stripe.']);
            }

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


        }else if($order->payment->name === "Stripe") {
            return $this->handleStripePayment($order, $total, $request);
        }

        // else if ($order->payment->name === "Stripe") {
        //     \Log::info("Début du traitement Stripe pour la commande", ['order_code' => $order->code]);

        //     // Vérifiez si la passerelle de paiement Stripe est configurée
        //     $paymentGateway = PaymentGateway::whereHas('payment', function ($query) {
        //         $query->where('name', 'Stripe');
        //     })->first();


        //     if (!$paymentGateway) {
        //         \Log::error("Passerelle de paiement Stripe non configurée.");
        //         return back()->withErrors(['error' => 'Passerelle de paiement Stripe non configurée.']);
        //     }

        //     // Optionnel : Conversion de devise si nécessaire (GBP -> USD, par exemple)
        //     \Log::info("Tentative de récupération du taux de conversion pour Stripe", ['from' => 'GBP', 'to' => 'USD']);
        //     $conversionRate = $this->getConversionRate('GBP', 'USD');

        //     if ($conversionRate <= 0) {
        //         \Log::error("Taux de conversion invalide ou non disponible pour Stripe", ['conversionRate' => $conversionRate]);
        //         return back()->withErrors(['error' => 'Impossible de récupérer le taux de conversion pour Stripe.']);
        //     }

        //     // Calculer le montant total en centimes (Stripe requiert des montants en centimes)
        //     $totalUSD = (int) round($total * $conversionRate * 100);
        //     \Log::info("Montant total calculé pour Stripe", ['totalUSD' => $totalUSD]);

        //     try {
        //         // Initialisation de Stripe avec les clés API
        //         \Log::info("Initialisation de Stripe", ['api_key' => $paymentGateway->api_key]);

        //         $stripe = new Stripe(
        //             $paymentGateway->api_key,
        //             $paymentGateway->secret_key
        //         );

        //         // Création de l'intention de paiement Stripe
        //         \Log::info("Création de l'intention de paiement Stripe", [
        //             'amount' => $totalUSD,
        //             'currency' => 'usd',
        //             'order_code' => $order->code,
        //         ]);

        //         $paymentIntent = $stripe->initiatePayment([
        //             'amount' => $totalUSD,
        //             'currency' => 'usd', // Assurez-vous que la devise correspond à votre paramétrage Stripe
        //             'description' => "Paiement pour la commande {$order->code}",
        //             'metadata' => [
        //                 'order_code' => $order->code,
        //                 'email' => $order->email,
        //                 'phone' => $order->phone,
        //             ],
        //         ]);

        //         // Vérifiez si l'intention de paiement contient une URL de redirection
        //         $paymentUrl = $paymentIntent['next_action']['redirect_to_url']['url'] ?? null;

        //         if (!$paymentUrl) {
        //             \Log::error("URL de redirection Stripe non disponible", ['paymentIntent' => $paymentIntent]);
        //             return back()->withErrors(['error' => 'Erreur : URL de redirection Stripe non disponible.']);
        //         }

        //         // Mettre à jour le statut de la commande
        //         \Log::info("Mise à jour de la commande avec l'intention de paiement Stripe", [
        //             'order_code' => $order->code,
        //             'payment_intent_id' => $paymentIntent['id'],
        //         ]);

        //         $order->update([
        //             'status' => 'pending_payment', // Exemple de statut
        //             'payment_intent_id' => $paymentIntent['id'], // Enregistrez l'identifiant Stripe pour suivi
        //         ]);

        //         // Redirigez l'utilisateur vers l'URL de paiement Stripe
        //         \Log::info("Redirection vers Stripe", ['paymentUrl' => $paymentUrl]);
        //         return redirect()->away($paymentUrl);

        //     } catch (\Exception $e) {
        //         \Log::error("Erreur Stripe lors du paiement", [
        //             'order_code' => $order->code,
        //             'error' => $e->getMessage(),
        //             'stack_trace' => $e->getTraceAsString(),
        //         ]);

        //         // Retournez une erreur à l'utilisateur
        //         return back()->withErrors(['error' => 'Erreur de paiement Stripe : ' . $e->getMessage()]);
        //     }
        // }
    }


    /**
     * Gère le paiement avec Stripe.
     *
     * @param Order $order
     * @param float $total
     * @return RedirectResponse
     */
    private function handleStripePayment(Order $order, float $total, Request $request)
    {
        \Log::info("Début du traitement Stripe pour la commande", ['order_code' => $order->code]);

        // Récupération des informations de passerelle Stripe depuis la table `payment_gateways`
        $paymentGateway = PaymentGateway::whereHas('payment', function ($query) {
            $query->where('name', 'Stripe');
        })->first();

        if (!$paymentGateway) {
            \Log::error("Passerelle de paiement Stripe non configurée.");
            return back()->withErrors(['error' => 'Passerelle de paiement Stripe non configurée.']);
        }

        // Initialisation du service Stripe avec les clés API de la table
        $stripeService = new Stripe($paymentGateway->api_key, $paymentGateway->secret_key);

        $totalInCents = (int) round($total * 100); // Stripe utilise des montants en centimes
        \Log::info("Montant total calculé pour Stripe", ['totalInCents' => $totalInCents]);

        try {
            $paymentIntent = $stripeService->initiatePayment([
                'amount' => $totalInCents,
                'currency' => 'usd',
                'description' => "Paiement pour la commande {$order->code}",
                'payment_method' => $request->payment_method, // Utilisez le payment_method transmis
                'confirm' => true, // Confirmer le paiement immédiatement
            ]);

            if ($paymentIntent['status'] !== 'succeeded') {
                \Log::warning("Paiement non complété", ['status' => $paymentIntent['status']]);
                return back()->withErrors(['error' => 'Le paiement n\'a pas été complété. Veuillez réessayer.']);
            }

            $order->update([
                'status' => 'paid',
                'payment_intent_id' => $paymentIntent['id'],
            ]);

            \Log::info("Paiement Stripe confirmé", ['paymentIntent' => $paymentIntent]);

            // Redirection vers une page de succès
            return redirect()->route('customer.orders.success', $order->id);
        } catch (\Exception $e) {
            \Log::error("Erreur Stripe lors du paiement", [
                'order_code' => $order->code,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors(['error' => 'Erreur de paiement Stripe : ' . $e->getMessage()]);
        }
    }

    private function handle3DSecureAction($paymentIntent)
    {
        // Si une authentification 3D Secure est nécessaire, gérer la logique ici
        if (isset($paymentIntent['next_action']['use_stripe_sdk'])) {
            return redirect()->away($paymentIntent['next_action']['use_stripe_sdk']['url']);
        }

        \Log::error("Action supplémentaire Stripe requise mais non prise en charge", ['paymentIntent' => $paymentIntent]);
        return back()->withErrors(['error' => 'Action supplémentaire Stripe requise mais non prise en charge.']);
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

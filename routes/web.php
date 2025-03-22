<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;



Route::get('locale/{lang}', [LocaleController::class, 'setLocale'])->name('set.locale');




// Gestion des erreurs 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::middleware(['exclude.customer'])->group(function () {
    Route::get('/welcome', function () {
        return view('welcome');
    })->name('home.welcome');
});

Route::middleware(['auth:customer'])->group(function () {
    Route::post('/likes', [LikeController::class, 'store']);
    Route::post('/likes/toggle', [LikeController::class, 'toggleLike']);
});

// Routes publiques pour les clients
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login'); // Affiche le formulaire de connexion
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('registerStore');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('loginStore');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->middleware('auth:customer')->name('logout');

    Route::get('commandes', [OrderController::class, 'customerOrders'])->name('orders.index');
    Route::get('commandes/{commande}', [OrderController::class, 'CustomerShowOrders'])->name('orders.show');
    Route::delete('commandes/{commande}', [OrderController::class, 'cancelOrder'])->name('orders.cancelOrder');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
Route::resource('blogs', BlogController::class);
Route::post('/blogs/{blog}/comments', [BlogController::class, 'storeComment'])->name('blogs.storeComment');


Route::resource('menus', MenuController::class);
Route::post('/cart/add', [MenuController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove', [MenuController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart/count', [MenuController::class, 'cartCount'])->name('cart.count');
Route::get('/cart', [MenuController::class, 'viewCart'])->name('cart.view');
// Affiche la page de validation de commande
Route::get('/checkout', [MenuController::class, 'viewCheckout'])->name('checkout.view');
// Route pour enregistrer la commande (POST)
Route::post('/checkout', [MenuController::class, 'storeOrder'])->name('checkout.store');
Route::get('/orders/success', function () {
    return redirect()->route('customer.orders.index')
        ->with('success', 'Paiement Réussi ! Votre commande a été confirmée avec succès. Merci pour votre achat');
})->name('customer.orders.success');
Route::post('/api/create-payment-intent', [MenuController::class, 'createPaymentIntent']);




// Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');


Route::middleware(['auth', 'verified','exclude.customer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');  // Vérifie le rôle 'admin' uniquement ici
});


Route::middleware(['auth', 'verified'])->group(function () {
    // Routes spécifiques au tableau de bord avec CheckRole
    Route::middleware(['role:administrator|super_administrator|manager|editor|viewer|exclude.customer'])->prefix('admin')->name('admin.')->group(function () {



    Route::get('translations', [TranslationController::class, 'index'])
        ->name('translations.index')
        ->middleware(['permission:view-translations']);
    Route::post('translations', [TranslationController::class, 'store'])
        ->name('translations.store')
        ->middleware(['permission:create-translations']);


        Route::get('translations/{translation}/edit', [TranslationController::class, 'edit'])
        ->name('translations.edit')
        ->middleware(['permission:edit-translations']);

    Route::put('translations/{translation}', [TranslationController::class, 'update'])
        ->name('translations.update')
        ->middleware(['permission:edit-translations']);


    Route::delete('translations/{translation}', [TranslationController::class, 'destroy'])
        ->name('translations.destroy')
        ->middleware(['permission:delete-translations']);

        // Fonctionnalité de traduction
        // Route::post('translations/translate', [TranslationController::class, 'translate'])
        //     ->name('translations.translate')
        //     ->middleware(['permission:create-translations']);



            Route::get('services', [ServiceController::class, 'index'])
                ->name('services.index')
                ->middleware(['permission:view-services']);
            Route::post('services', [ServiceController::class, 'store'])
                ->name('services.store')
                ->middleware(['permission:create-services']);
                Route::put('services/{service}', [ServiceController::class, 'update'])
                ->name('services.update')
                ->middleware(['permission:edit-services']);
            Route::delete('services/{service}', [ServiceController::class, 'destroy'])
                ->name('services.destroy')
                ->middleware(['permission:delete-services']);


        Route::get('shippings', [ShippingController::class, 'index'])
            ->name('shippings.index')
            ->middleware(['permission:view-shippings']);

        Route::get('shippings/create', [ShippingController::class, 'create'])
            ->name('shippings.create')
            ->middleware(['permission:create-shippings']);

        Route::post('shippings', [ShippingController::class, 'store'])
            ->name('shippings.store')
            ->middleware(['permission:create-shippings']);

        Route::get('shippings/{shipping}', [ShippingController::class, 'show'])
            ->name('shippings.show')
            ->middleware(['permission:view-shippings']);

        Route::get('shippings/{shipping}/edit', [ShippingController::class, 'edit'])
            ->name('shippings.edit')
            ->middleware(['permission:edit-shippings']);

        Route::put('shippings/{shipping}', [ShippingController::class, 'update'])
            ->name('shippings.update')
            ->middleware(['permission:edit-shippings']);

        Route::delete('shippings/{shipping}', [ShippingController::class, 'destroy'])
            ->name('shippings.destroy')
            ->middleware(['permission:delete-shippings']);

            // Payments
        Route::get('payments', [PaymentController::class, 'index'])
        ->name('payments.index')
        ->middleware(['permission:view-payments']);

        Route::get('payments/create', [PaymentController::class, 'create'])
        ->name('payments.create')
        ->middleware(['permission:create-payments']);

        Route::post('payments', [PaymentController::class, 'store'])
        ->name('payments.store')
        ->middleware(['permission:create-payments']);

        Route::get('payments/{payment}', [PaymentController::class, 'show'])
        ->name('payments.show')
        ->middleware(['permission:view-payments']);

        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])
        ->name('payments.edit')
        ->middleware(['permission:edit-payments']);

        Route::put('payments/{payment}', [PaymentController::class, 'update'])
        ->name('payments.update')
        ->middleware(['permission:edit-payments']);

        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])
        ->name('payments.destroy')
        ->middleware(['permission:delete-payments']);


        Route::get('gateways', [PaymentController::class, 'indexGateway'])
        ->name('gateways.index')
        ->middleware(['permission:view-gateways']);

        Route::get('gateways/create', [PaymentController::class, 'createGateway'])
        ->name('gateways.create')
        ->middleware(['permission:create-gateways']);

        Route::post('gateways', [PaymentController::class, 'storeGateway'])
        ->name('gateways.store')
        ->middleware(['permission:create-gateways']);

        Route::get('gateways/{gateway}', [PaymentController::class, 'showGateway'])
        ->name('gateways.show')
        ->middleware(['permission:view-gateways']);

        Route::get('gateways/{gateway}/edit', [PaymentController::class, 'editGateway'])
        ->name('gateway.edit')
        ->middleware(['permission:edit-gateways']);

        Route::put('gateways/{gateway}', [PaymentController::class, 'updateGateway'])
        ->name('gateways.update')
        ->middleware(['permission:edit-gateways']);

        Route::delete('gateways/{gateway}', [PaymentController::class, 'destroyGateway'])
        ->name('gateways.destroy')
        ->middleware(['permission:delete-gateways']);


        // Route::resource('commandes', OrderController::class);

        // Commandes
        Route::get('commandes', [OrderController::class, 'index'])
            ->name('commandes.index')
            ->middleware(['permission:view-commandes|view-orders']);
        Route::get('commandes/create', [OrderController::class, 'create'])
            ->name('commandes.create')
            ->middleware(['permission:create-commandes|create-orders']);
        Route::post('commandes', [OrderController::class, 'store'])
            ->name('commandes.store')
            ->middleware(['permission:create-commandes|create-orders']);
        Route::get('commandes/{commande}', [OrderController::class, 'show'])
            ->name('commandes.show')
            ->middleware(['permission:view-commandes|view-orders']);
        Route::get('commandes/{commande}/edit', [OrderController::class, 'edit'])
            ->name('commandes.edit')
            ->middleware(['permission:edit-commandes|edit-orders']);
        Route::put('commandes/{commande}', [OrderController::class, 'update'])
            ->name('commandes.update')
            ->middleware(['permission:edit-commandes|edit-orders']);
        Route::delete('commandes/{commande}', [OrderController::class, 'destroy'])
            ->name('commandes.destroy')
            ->middleware(['permission:delete-commandes|delete-orders']);


        // Catégories
        Route::get('categories', [CategoryController::class, 'index'])
            ->name('categories.index')
            ->middleware(['permission:view-categories']);
        Route::get('categories/create', [CategoryController::class, 'create'])
            ->name('categories.create')
            ->middleware(['permission:create-categories']);
        Route::post('categories', [CategoryController::class, 'store'])
            ->name('categories.store')
            ->middleware(['permission:create-categories']);
        Route::get('categories/{category}', [CategoryController::class, 'show'])
            ->name('categories.show')
            ->middleware(['permission:view-categories']);
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit')
            ->middleware(['permission:edit-categories']);
        Route::put('categories/{category}', [CategoryController::class, 'update'])
            ->name('categories.update')
            ->middleware(['permission:edit-categories']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy')
            ->middleware(['permission:delete-categories']);

        // Articles
        Route::get('articles', [ArticleController::class, 'index'])
            ->name('articles.index')
            ->middleware(['permission:view-articles']);
        Route::get('articles/create', [ArticleController::class, 'create'])
            ->name('articles.create')
            ->middleware(['permission:create-articles']);
        Route::post('articles', [ArticleController::class, 'store'])
            ->name('articles.store')
            ->middleware(['permission:create-articles']);
        Route::get('articles/{article}', [ArticleController::class, 'show'])
            ->name('articles.show')
            ->middleware(['permission:view-articles']);
        Route::get('articles/{article}/edit', [ArticleController::class, 'edit'])
            ->name('articles.edit')
            ->middleware(['permission:edit-articles']);
        Route::put('articles/{article}', [ArticleController::class, 'update'])
            ->name('articles.update')
            ->middleware(['permission:edit-articles']);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])
            ->name('articles.destroy')
            ->middleware(['permission:delete-articles']);

        // Route spécifique pour ajouter des commentaires aux articles
        Route::post('articles/{article}/comments', [ArticleController::class, 'storeComment'])
            ->name('articles.storeComment')
            ->middleware(['permission:create-comments']);

        // Produits
        Route::get('products', [ProductController::class, 'index'])
            ->name('products.index')
            ->middleware(['permission:view-products']);
        Route::get('products/create', [ProductController::class, 'create'])
            ->name('products.create')
            ->middleware(['permission:create-products']);
        Route::post('products', [ProductController::class, 'store'])
            ->name('products.store')
        ->middleware(['permission:create-products']);
        Route::get('products/{product}', [ProductController::class, 'show'])
            ->name('products.show')
            ->middleware(['permission:view-products']);
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])
            ->name('products.edit')
            ->middleware(['permission:edit-products']);
        Route::put('products/{product}', [ProductController::class, 'update'])
            ->name('products.update')
            ->middleware(['permission:edit-products']);
        Route::delete('products/{product}', [ProductController::class, 'destroy'])
            ->name('products.destroy')
            ->middleware(['permission:delete-products']);

        // Rôles
        Route::get('roles', [RoleController::class, 'index'])
            ->name('roles.index')
            ->middleware(['permission:view-roles']);
        Route::get('roles/create', [RoleController::class, 'create'])
            ->name('roles.create')
            ->middleware(['permission:create-roles']);
        Route::post('roles', [RoleController::class, 'store'])
            ->name('roles.store')
            ->middleware(['permission:create-roles']);
        Route::get('roles/{role}', [RoleController::class, 'show'])
            ->name('roles.show')
            ->middleware(['permission:view-roles']);
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
            ->name('roles.edit')
            ->middleware(['permission:edit-roles']);
        Route::put('roles/{role}', [RoleController::class, 'update'])
            ->name('roles.update')
            ->middleware(['permission:edit-roles']);
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])
            ->name('roles.destroy')
            ->middleware(['permission:delete-roles']);

        // Permissions
        // Route::resource('permissions', PermissionController::class);

        // // Coupons
        // Route::resource('coupons', CouponController::class);

        // Ressources pour les permissions
        // Permissions
        Route::get('permissions', [PermissionController::class, 'index'])
            ->name('permissions.index')
            ->middleware(['permission:view-permissions']);

        Route::get('permissions/create', [PermissionController::class, 'create'])
            ->name('permissions.create')
            ->middleware(['permission:create-permissions']);

        Route::post('permissions', [PermissionController::class, 'store'])
            ->name('permissions.store')
            ->middleware(['permission:create-permissions']);

        Route::get('permissions/{permission}', [PermissionController::class, 'show'])
            ->name('permissions.show')
            ->middleware(['permission:view-permissions']);

        Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])
            ->name('permissions.edit')
            ->middleware(['permission:edit-permissions']);

        Route::put('permissions/{permission}', [PermissionController::class, 'update'])
            ->name('permissions.update')
            ->middleware(['permission:edit-permissions']);

        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])
            ->name('permissions.destroy')
            ->middleware(['permission:delete-permissions']);

            // Coupons
            Route::get('coupons', [CouponController::class, 'index'])
            ->name('coupons.index')
            ->middleware(['permission:view-coupons']);

            Route::get('coupons/create', [CouponController::class, 'create'])
            ->name('coupons.create')
            ->middleware(['permission:create-coupons']);

            Route::post('coupons', [CouponController::class, 'store'])
            ->name('coupons.store')
            ->middleware(['permission:create-coupons']);

            Route::get('coupons/{coupon}', [CouponController::class, 'show'])
            ->name('coupons.show')
            ->middleware(['permission:view-coupons']);

            Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])
            ->name('coupons.edit')
            ->middleware(['permission:edit-coupons']);

            Route::put('coupons/{coupon}', [CouponController::class, 'update'])
            ->name('coupons.update')
            ->middleware(['permission:edit-coupons']);

            Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])
            ->name('coupons.destroy')
            ->middleware(['permission:delete-coupons']);


            });
    // Gestion des utilisateurs accessible à Admin et SuperAdmin
    Route::middleware(['role:administrator|super_administrator'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});



require __DIR__.'/auth.php';

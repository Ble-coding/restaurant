<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['exclude.customer'])->group(function () {
    Route::get('/welcome', function () {
        return view('welcome');
    });
});


Route::middleware(['auth:customer'])->group(function () {
    Route::post('/likes', [LikeController::class, 'store']);
    Route::post('/likes/toggle', [LikeController::class, 'toggleLike']);
});


// Routes publiques pour les clients
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login'); // Affiche le formulaire de connexion
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->middleware('auth:customer')->name('logout');

    Route::get('commandes', [OrderController::class, 'customerOrders'])->name('orders.index');
    Route::get('commandes/{commande}', [OrderController::class, 'CustomerShowOrders'])->name('orders.show');
    Route::delete('/commandes/{commande}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancelOrder');

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


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');  // Vérifie le rôle 'admin' uniquement ici
});


Route::middleware(['auth', 'verified'])->group(function () {
    // Routes spécifiques au tableau de bord avec CheckRole
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('articles', ArticleController::class);
        Route::post('/articles/{article}/comments', [ArticleController::class, 'storeComment'])->name('articles.storeComment');

        // Ressources pour les rôles
        Route::resource('products', ProductController::class);

        Route::resource('commandes', OrderController::class);

        // Ressources pour les rôles
        Route::resource('roles', RoleController::class);

        // Ressources pour les permissions
        Route::resource('permissions', PermissionController::class);
    });

    // Gestion des utilisateurs accessible à Admin et SuperAdmin
    Route::middleware(['role:Admin|SuperAdmin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::resource('coupons', CouponController::class);
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});



require __DIR__.'/auth.php';

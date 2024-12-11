<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\Product;

class CartService
{
    public function getCart()
    {
        return session('cart', []);
    }

    public function addToCart($productId)
    {
        $cart = session()->get('cart', []);
        $product = Product::find($productId);

        if (!$product) {
            return $cart;
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);
        return $cart;
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return $cart;
    }

    public function calculateSubtotal()
    {
        $cart = $this->getCart();
        return collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function clearCart()
    {
        session()->forget('cart');
    }

    public function updateCartQuantities($productId, $quantity)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        return $cart;
    }
}

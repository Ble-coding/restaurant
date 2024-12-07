<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function checkout()
    {
        $cart = Cart::where('session_id', session()->getId())->first();
        if (!$cart) return redirect()->back()->with('error', 'Cart is empty.');

        $order = Order::create([
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'total' => $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            }),
            'status' => 'pending',
        ]);

        $cart->delete(); // Vider le panier après la commande

        return redirect()->route('orders.success')->with('success', 'Order placed successfully.');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

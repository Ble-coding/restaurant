<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderLog;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = trim($request->get('search'));

        $orders = Order::with('products')->with('customer')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('products', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                             ->orWhere('code', 'like', '%' . $search . '%')
                             ->orWhere('status', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Pagination

        return view('admin.orders.index', compact('orders'));
    }

    public function customerOrders()
    {
        $customerId = Auth::guard('customer')->id();

        $orders = Order::with('products')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Pagination

        return view('menus.orders.index', compact('orders'));
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
    public function show(string $commandeId)
    {
        $order = Order::where('id', $commandeId)
             ->with('orderLogs')
                        ->first();

        if (!$order) {
            return redirect()->route('admin.orders.index')->with('error', "Commande non trouvée.");
        }

            // Récupérer la date du statut "delivered"
            $deliveryLog = $order->orderLogs
            ->where('status_after', 'delivered') // Filtrer les logs avec le statut 'delivered'
            ->sortByDesc('created_at') // Trier par la date la plus récente (si plusieurs logs existent)
            ->first();

        $deliveryDate = $deliveryLog ? $deliveryLog->created_at : null;

        // Récupérer la date du statut "canceled"
        $cancelLog = $order->orderLogs
        ->where('status_after', 'canceled') // Filtrer les logs avec le statut 'canceled'
        ->sortByDesc('created_at') // Trier par la date la plus récente (si plusieurs logs existent)
        ->first();

        $cancelDate = $cancelLog ? $cancelLog->created_at : null;


        // Retourne la vue avec les données de la commande
        return view('admin.orders.show', compact('order', 'deliveryDate','cancelDate'));
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
        $request->validate([
            'status' => 'required|in:pending,preparing,shipped,delivered,canceled',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;

        if (in_array($oldStatus, ['shipped', 'delivered'])) {
            return back()->withErrors('La commande ne peut pas être modifiée après expédition.');
        }

        $order->update([
            'status' => $request->status,
        ]);

        // Enregistrer l'historique
        $order->orderLogs()->create([
            'status_before' => $oldStatus,
            'status_after' => $request->status,
            'changed_by' => Auth::user()->name ?? 'Client',
        ]);

        return redirect()->route('admin.commandes.index')
                         ->with('success', 'Statut de la commande mis à jour.');
    }

    public function cancelOrder($id)
    {
        $order = Order::where('customer_id', Auth::guard('customer')->id())
                    ->where('status', 'pending')
                    ->findOrFail($id);

        $order->update(['status' => 'canceled']);

        $order->orderLogs()->create([
            'status_before' => 'pending',
            'status_after' => 'canceled',
            'changed_by' => Auth::guard('customer')->name,
        ]);



        return redirect()->route('customer.orders.index')
                        ->with('success', 'Commande annulée.');
    }


    public function CustomerShowOrders($commandeId)
    {
        // Vérification d'authentification
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return redirect()->route('login')->with('error', "Vous devez être connecté pour voir vos commandes.");
        }

        // Récupération de la commande liée à l'utilisateur connecté
        $order = Order::where('id', $commandeId)
         ->with('orderLogs')
                        ->where('customer_id', $customer->id)
                        ->first();

        if (!$order) {
            return redirect()->route('customer.orders.index')->with('error', "Commande non trouvée.");
        }

            // Récupérer la date du statut "delivered"
            $deliveryLog = $order->orderLogs
            ->where('status_after', 'delivered') // Filtrer les logs avec le statut 'delivered'
            ->sortByDesc('created_at') // Trier par la date la plus récente (si plusieurs logs existent)
            ->first();

        $deliveryDate = $deliveryLog ? $deliveryLog->created_at : null;


             // Récupérer la date du statut "canceled"
             $cancelLog = $order->orderLogs
             ->where('status_after', 'canceled') // Filtrer les logs avec le statut 'canceled'
             ->sortByDesc('created_at') // Trier par la date la plus récente (si plusieurs logs existent)
             ->first();

             $cancelDate = $cancelLog ? $cancelLog->created_at : null;

        // Retourne la vue avec les données de la commande
        return view('menus.orders.show', compact('order','cancelDate','deliveryDate'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

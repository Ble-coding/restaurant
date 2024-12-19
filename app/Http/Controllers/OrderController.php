<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderLog;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


class OrderController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('permission:view-commandes|view-orders')->only(['index', 'show']);
    //     $this->middleware('permission:create-commandes|create-orders')->only(['create', 'store']);
    //     $this->middleware('permission:edit-commandes|edit-orders')->only(['edit', 'update']);
    //     $this->middleware('permission:delete-commandes|delete-orders')->only('destroy');
    // }

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
            ->paginate(6); // Pagination

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

        // $this->authorize('view-orders');
        $order = Order::where('id', $commandeId)
            ->with('orderLogs')
            ->first();

        if (!$order) {
            // Redirection si la commande n'est pas trouvée
            return redirect()->route('admin.orders.index')->with('error', "Commande non trouvée.");
        }

        // Extraction des dates pour les statuts
        $deliveryLog = $order->orderLogs
            ->where('status_after', 'delivered')
            ->sortByDesc('created_at')
            ->first();

        $deliveryDate = $deliveryLog ? $deliveryLog->created_at : null;

        $cancelLog = $order->orderLogs
            ->where('status_after', 'canceled')
            ->sortByDesc('created_at')
            ->first();

        $cancelDate = $cancelLog ? $cancelLog->created_at : null;

        $shippingCost = $order->shipping_cost;

        return view('admin.orders.show', compact('order', 'deliveryDate', 'cancelDate', 'shippingCost'));
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

        // $this->authorize('update-orders');
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
        // Vérification de l'utilisateur connecté sous le guard 'customer'
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            // Redirection si l'utilisateur n'est pas connecté
            return redirect()->route('customer.login')->with('error', "Vous devez être connecté pour voir vos commandes.");
        }

        // Récupération de la commande associée à ce client spécifique
        $order = Order::where('id', $commandeId)
            ->with('orderLogs') // Chargement des logs associés à la commande
            ->where('customer_id', $customer->id) // Vérification que la commande appartient bien au client connecté
            ->first();

        if (!$order) {
            // Si la commande n'existe pas ou ne correspond pas à ce client
            return redirect()->route('customer.orders.index')->with('error', "Commande non trouvée.");
        }

        // Récupération de la date du statut "delivered"
        $deliveryLog = $order->orderLogs
            ->where('status_after', 'delivered') // Recherche du log avec statut 'delivered'
            ->sortByDesc('created_at') // Tri pour prendre le plus récent
            ->first();

        $deliveryDate = $deliveryLog ? $deliveryLog->created_at : null; // Extraction de la date si trouvée

        // Récupération de la date du statut "canceled"
        $cancelLog = $order->orderLogs
            ->where('status_after', 'canceled')
            ->sortByDesc('created_at')
            ->first();

        $cancelDate = $cancelLog ? $cancelLog->created_at : null;

        // Extraction des frais de livraison
        $shippingCost = $order->shipping_cost;

        // Retourne la vue avec toutes les données nécessaires
        return view('menus.orders.show', compact('order', 'cancelDate', 'deliveryDate', 'shippingCost'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

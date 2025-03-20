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
        $status = $request->get('status');
        $date = $request->get('date');
        $price = $request->get('price');

        $orders = Order::with('products', 'customer')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                      ->orWhere('total', 'like', '%' . $search . '%')
                      ->orWhereHas('customer', function ($subQuery) use ($search) {
                          $subQuery->where('first_name', 'like', '%' . $search . '%')
                                   ->orWhere('last_name', 'like', '%' . $search . '%');
                      });
                });
            });

        // Appliquer les filtres ensemble si au moins un est défini
        if ($status || $date || $price) {
            $orders->where(function ($q) use ($status, $date, $price) {
                if ($status) {
                    $q->where('status', $status);
                }
                if ($date) {
                    $q->whereDate('created_at', $date);
                }
                if ($price) {
                    $q->whereRaw("CAST(total AS DECIMAL(10,2)) >= ?", [$price]);
                }
            });
        }

        $orders = $orders->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function customerOrders(Request $request)
    {
        $customerId = Auth::guard('customer')->id();

        $search = trim($request->get('search'));
        $status = $request->get('status');
        $date = $request->get('date');
        $price = $request->get('price');

        $orders = Order::with('products')
            ->where('customer_id', $customerId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', '%' . $search . '%')
                      ->orWhere('total', 'like', '%' . $search . '%');
                });
            });

        // Appliquer les filtres ensemble si au moins un est défini
        if ($status || $date || $price) {
            $orders->where(function ($q) use ($status, $date, $price) {
                if ($status) {
                    $q->whereJsonContains('status->en', $status)
                      ->orWhereJsonContains('status->fr', $status);
                }
                if ($date) {
                    $q->whereDate('created_at', $date);
                }
                if ($price) {
                    $q->whereRaw("CAST(total AS DECIMAL(10,2)) >= ?", [$price]);
                }
            });
        }

        $orders = $orders->orderBy('created_at', 'desc')->paginate(10);

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
            return redirect()->route('admin.orders.index')->with('error', __('order.not_found'));
        }

        // Extraction du statut en anglais uniquement
        $rawStatus = $order->getRawStatus();

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
        $request->validate([
            'status' => 'required|in:pending,preparing,shipped,delivered,canceled',
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->getRawStatus(); // On récupère toujours la version EN

        if (in_array($oldStatus, ['shipped', 'delivered'])) {
            return back()->withErrors(__('order.cannot_be_modified'));
        }

        $order->update([
            'status' => $request->status,
        ]);

        $order->orderLogs()->create([
            'status_before' => $oldStatus,
            'status_after' => $request->status,
            'changed_by' => Auth::user()->name ?? 'Client',
        ]);

        return redirect()->route('admin.commandes.index')
                         ->with('success', __('order.status_updated'));
    }
    public function cancelOrder($id)
    {

        $customer = Auth::guard('customer')->user();
        // $customerId = Auth::guard('customer')->id();
        if (!$customer || empty($customer->last_name)) {
            return redirect()->route('customer.login')->with('error', __('order.must_be_logged_in'));
        }

       

        // Récupérer la commande appartenant au client connecté
        $order = Order::where('customer_id', Auth::guard('customer')->id())
                      ->findOrFail($id);

                      
    
        // Vérifier si la commande est annulable (statut "pending")
        if ($order->getTranslation('status', 'en') !== 'pending') {
            return back()->withErrors(__('order.cannot_be_canceled'));
        }
    
        // Mettre à jour le statut de la commande avec Spatie Translatable
        $order->setTranslation('status', 'en', 'canceled');
        $order->setTranslation('status', 'fr', 'Annulé');

        // dd($order);
        $order->save();
    
        // Enregistrer le changement dans les logs
        $order->orderLogs()->create([
            'status_before' => 'pending',
            'status_after' => 'canceled',
            // 'changed_by' => Auth::guard('customer')->user()->name,
            'changed_by' => $customer->last_name, // Correction ici
            // 'changed_by' =>  Auth::guard('customer')->name(),
        ]);
    
        // Redirection avec un message de succès
        return redirect()->route('customer.orders.index')
                         ->with('success', __('order.canceled_success'));
    }
    

    public function CustomerShowOrders($commandeId)
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return redirect()->route('customer.login')->with('error', __('order.must_be_logged_in'));
        }

        $order = Order::where('id', $commandeId)
            ->with('orderLogs')
            ->where('customer_id', $customer->id)
            ->first();

        if (!$order) {
            return redirect()->route('customer.orders.index')->with('error', __('order.not_found'));
        }

        $rawStatus = $order->getRawStatus(); // Toujours travailler avec EN

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

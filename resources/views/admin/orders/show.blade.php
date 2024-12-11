@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>ID Commande</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet"  href="{{ asset('assets/css/menuCheckout.css') }}">
    <link rel="stylesheet"  href="{{ asset('assets/css/menuId.css') }}">
@endpush

@section('content')

<div class="container my-5">
    <div class="checkout-container m-5">
        <h3 class="mb-4">Détails de la commande {{ $order->code }}</h3>
        <!-- Informations sur la commande -->
        <div class="row">
            <div class="col-md-6">
                <h5>Informations de facturation</h5>
                <p><strong class="menu-item-title">Prénom :</strong> {{ $order->first_name }}</p>
                <p><strong class="menu-item-title">Nom :</strong> {{ $order->last_name }}</p>
                <p><strong class="menu-item-title">Email :</strong> {{ $order->email }}</p>
                <p><strong class="menu-item-title">Téléphone :</strong> +{{ $order->country_code }}{{ $order->phone }}</p>
                <p><strong class="menu-item-title">Adresse :</strong> {{ $order->address }}</p>
                <p><strong class="menu-item-title">Ville :</strong> {{ $order->city }}</p>
                <p><strong class="menu-item-title">Code postal :</strong> {{ $order->zip }}</p>
                <p><strong class="menu-item-title">Notes :</strong> {{ $order->order_notes ?? 'Aucune note' }}</p>
            </div>

            <div class="col-md-6">
                <h5>Informations sur la commande</h5>
                <p><strong class="menu-item-title">Date :</strong> {{ $order->created_at->translatedFormat('j F, Y') }}</p>
                <p><strong class="menu-item-title">Total :</strong> {{ number_format($order->total, 2) }} €</p>
                <p><strong class="menu-item-title">Statut :</strong> {{ ucfirst($order->getStatusLabel()) }}</p>
                @if ($order->coupon)
                    <p><strong class="menu-item-title">Coupon utilisé :</strong> {{ $order->coupon->code }}</p>
                @endif

                @if ($deliveryDate)
                    <p><strong class="menu-item-title">Date de livraison :</strong> {{ $deliveryDate->format('d/m/Y H:i') }}</p>
                @else
                    {{-- <p><strong>Date de livraison :</strong> Non livré</p> --}}
                @endif

                <span class="menu-badge">
                    effectuée depuis le compte {{ $order->customer->first_name }} {{ $order->customer->last_name }}
                </span>
            </div>
        </div>

        <!-- Résumé de la commande -->
        <h5 class="mt-4">Résumé des produits</h5>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}" class="product-img" style="width: 80px; height: auto;">
                            <span class="menu-item-title">{{ $product->name }}</span>
                        </div>
                    </td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td class="menu-item-price">£{{ number_format($product->pivot->price, 2) }}</td>
                    <td>£{{ number_format($product->pivot->price * $product->pivot->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Sous-total</th>
                    <th>£{{ number_format($order->total, 2) }}</th>
                </tr>
            </tfoot>
        </table>
        <p class="footer-text mt-3">
            Vos données personnelles seront utilisées pour traiter votre commande, améliorer votre expérience sur ce site et pour d'autres fins décrites dans notre <a href="#">politique de confidentialité</a>.
        </p>
    </div>
</div>
@endsection

@push('scriptstoggle')
  <script src="{{ asset('assets/js/toggleCouponSection.js') }}"></script>

@endpush


@push('scriptsCheckout')
<script src="{{ asset('assets/js/global.js') }}"></script>
@endpush


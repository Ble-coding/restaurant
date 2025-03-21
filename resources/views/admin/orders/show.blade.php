@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('order.title') }}</h1>
            <p>{{ __('order.description') }}</p>
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
        <h3 class="mb-4">{{ __('order.order_details', ['code' => $order->code]) }}</h3>
        <!-- Informations sur la commande -->
        <div class="row">
            <div class="col-md-6">
                <h5>{{ __('order.billing_info') }}</h5>
                <p><strong class="menu-item-title">{{ __('order.first_name') }} :</strong> {{ $order->first_name }}</p>
                <p><strong class="menu-item-title">{{ __('order.last_name') }} :</strong> {{ $order->last_name }}</p>
                <p><strong class="menu-item-title">{{ __('order.email') }} :</strong> {{ $order->email }}</p>
                <p><strong class="menu-item-title">{{ __('order.phone') }} :</strong> +{{ $order->country_code }}{{ $order->phone }}</p>
                <p><strong class="menu-item-title">{{ __('order.address') }} :</strong> {{ $order->address }}</p>
                <p><strong class="menu-item-title">{{ __('order.city') }} :</strong> {{ $order->city }}</p>
                <p><strong class="menu-item-title">{{ __('order.zip') }} :</strong> {{ $order->zip }}</p>
                <p><strong class="menu-item-title">{{ __('order.notes') }} :</strong> {{ $order->order_notes ?? __('order.no_notes') }}</p>
            </div>

            <div class="col-md-6">
                <h5>{{ __('order.order_info') }}</h5>
                <p><strong class="menu-item-title">{{ __('order.date') }} :</strong> {{ $order->created_at->translatedFormat('j F, Y') }}</p>
                <p><strong class="menu-item-title">{{ __('order.total') }} :</strong> £{{ number_format($order->total, 2) }} </p>
                <p><strong class="menu-item-title">{{ __('order.status') }} :</strong>  {{ $order->getTranslation('status', app()->getLocale()) }}</p>
                @if ($order->coupon)
                    <p><strong class="menu-item-title">{{ __('order.coupon_used') }} :</strong> {{ $order->coupon->code }}</p>
                @endif

                @if ($deliveryDate)
                    <p><strong class="menu-item-title">{{ __('order.delivery_date') }} :</strong> {{ $deliveryDate->format('d/m/Y H:i') }}</p>
                @else
                    {{-- <p><strong>{{ __('order.delivery_date') }} :</strong> {{ __('order.not_delivered') }}</p> --}}
                @endif

                <p><strong class="menu-item-title">{{ __('order.zone') }} :</strong> {{ $order->zone->name ?? __('order.not_specified') }}</p>
                <p><strong class="menu-item-title">{{ __('order.payment_method') }} :</strong> {{ $order->payment->name ?? __('order.not_specified') }}</p>

                <span class="menu-badge">
                    {{ __('order.account', ['first_name' => $order->customer->first_name, 'last_name' => $order->customer->last_name]) }}
                </span>
            </div>
        </div>

        <!-- Résumé de la commande -->
        <h5 class="mt-4">{{ __('order.product_summary') }}</h5>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>{{ __('order.product') }}</th>
                    <th>{{ __('order.quantity') }}</th>
                    <th>{{ __('order.unit_price') }}</th>
                    <th>{{ __('order.subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $product)
                    @php
                        // Récupération de la taille depuis les données pivot
                        $size = $product->pivot->size ?? '1_litre';
                        $sizeValue = $size === 'half_litre' ? 0.5 : 1; // Valeur en litres
                        $totalSize = $sizeValue * $product->pivot->quantity; // Quantité totale en litres
                        $lineTotal = $product->pivot->price * $product->pivot->quantity; // Total par ligne
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset('storage/' . $product['image']) }}"
                                     alt="{{ $product['name'] }}"
                                     class="product-img"
                                     style="width: 80px; height: auto;">
                                <span class="menu-item-title">{{ $product->name }}</span>
                            </div>
                        </td>
                        <td>
                            {{ $product->pivot->quantity }} ×
                            @if ($size === 'half_litre')
                                0.5
                            @else
                                1
                            @endif
                            = <strong>{{ $totalSize }}
                                {{-- Litre(s) --}}
                            </strong>
                        </td>
                        <td class="menu-item-price">£{{ number_format($product->pivot->price, 2) }}</td>
                        <td>£{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php
                    $subtotal = $order->products->sum(function($product) {
                        return $product->pivot->price * $product->pivot->quantity;
                    });

                    $shippingCost = $order->shipping_cost;
                    $total = $subtotal + $shippingCost; // Calcul total
                    $deposit = $subtotal * 0.5; // Acompte (50%)
                @endphp
                <tr>
                    <th colspan="3">{{ __('order.subtotal') }}</th>
                    <th>£{{ number_format($subtotal, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="3">{{ __('order.deposit') }}</th>
                    <th>£{{ number_format($deposit, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="3">{{ __('order.shipping_cost') }}</th>
                    <th>£{{ number_format($shippingCost, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="3">{{ __('order.total') }}</th>
                    <th>£{{ number_format($total, 2) }}</th>
                </tr>
            </tfoot>

        </table>
        <p class="footer-text mt-3">
            {!! __('order.footer_text') !!}
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

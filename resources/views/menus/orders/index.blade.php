


@extends('layouts.master')


@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/menuId.css') }}">
@endpush

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Notre univers culinaire !</h1>
            <p>Découvrez un menu soigneusement élaboré pour éveiller vos papilles et satisfaire toutes vos envies.</p>
        </div>
    </div>
@endsection

@section('content')


    <div class="container my-5">

        <div class="search-wrapper mb-4">
            <div class="search-container">
                <form method="GET" action="{{ route('customer.orders.index') }}" id="search-form" class="d-flex">
                    <input
                        type="text"
                        id="search"
                        class="form-control form-custom-user search-input"
                        name="search"
                        placeholder="Rechercher par produit..."
                        value="{{ request()->get('search') }}"
                    >
                </form>
            </div>
        </div>


         <!-- Début des items de menu -->
         <div class="row">
            @if(session('success'))
                <div class="alert alert-success" id="success-alert">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger"  id="error-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Début des items de menu -->
            <div class="row">
                @if($orders->isEmpty())
                    <p>Aucune commande trouvée.</p>
                @else
                    @foreach ($orders as $order)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="menu-item p-3">
                                <div class="menu-item-content">
                                    <div class="menu-item-header">

                                        <h3 class="menu-item-title">
                                            {{ $order->first_name }} {{ $order->last_name }}
                                        </h3>

                                        <div class="menu-item-dots"></div>

                                        <div class="menu-item-price">
                                            £ {{ number_format($order->total, 2) }}
                                        </div>
                                    </div>

                                    <p class="menu-item-description">
                                        <span class="menu-badge">
                                            {{ $order->created_at->translatedFormat('j F, Y') }} -
                                            ({{ $order->getStatusLabel() }})</span>

                                        @unless(in_array($order->status, \App\Models\Order::NON_MODIFIABLE_STATUSES))
                                            <a href="#" class="add_cart m-3" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $order->id }}">🗑️</a>
                                        @endunless
                                        <a class="{{ Route::currentRouteName() === 'customer.orders.show' ? 'active' : '' }}" href="{{ route('customer.orders.show', $order->id) }}">👀</a>

                                    </p>

                                    <ul class="menu-item-products">
                                        @php
                                            $productCount = $order->products->sum('pivot.quantity');
                                        @endphp

                                        <strong> {{ $order->code }}  {{ \Illuminate\Support\Str::plural('Produit', $productCount) }} : {{ $productCount }}</strong>

                                        {{-- @foreach($order->products as $product)
                                            <li>{{ $product->name }} (x{{ $product->pivot->quantity }})</li>
                                        @endforeach --}}

                                        {{-- <strong class="compte"> Id compte : {{ $order->customer->first_name }} {{ $order->customer->last_name }}</strong> --}}
                                    </ul>

                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="deleteModal{{ $order->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $order->id }}">Annuler la Commande {{ $order->code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes-vous sûr de vouloir annuler cette commande ? Cette action est irréversible.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST" action="{{ route('customer.orders.cancelOrder', $order->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn view-cart ">Confirmer</button>
                                        </form>
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Annuler</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    @endforeach
                @endif
            </div>

            <!-- Fin des items de menu -->
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="pagination-container">
                    {{ $orders->links('vendor.pagination.custom') }}
                </div>
            </div>

        </div>


    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/search.js') }}"></script>
@endpush

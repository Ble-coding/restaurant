


@extends('layouts.masterAdmin')


@push('styles')
    <link rel="stylesheet"  href="{{ asset('assets/css/menuSelect.css') }}">
@endpush
@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('order.title') }}</h1>
            <p>{{ __('order.description') }}</p>
        </div>
    </div>
@endsection

@section('content')


    <div class="container my-5">

        <div class="search-wrapper mb-4">
            <div class="">
                <form method="GET" action="{{ route('admin.commandes.index') }}" id="search-form">
                    <div class="row">
                        <!-- Recherche par mot-clé (Nom, Code) -->
                        <div class="col-md-6 mb-3">
                            <input
                                type="text"
                                id="search"
                                class="form-control form-custom-user"
                                name="search"
                                placeholder="{{ __('order.search_placeholder') }}"
                                value="{{ request()->get('search') }}"
                            >
                        </div>

                        <!-- Filtrer par statut -->
                        <div class="col-md-6 mb-3">
                            <select name="status" id="status" class="form-select form-custom-user">
                                <option value="">{{ __('order.status_filter') }}</option>
                                @foreach (\App\Models\Order::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ request()->get('status') === $key ? 'selected' : '' }}>
                                    {{ __($label) }}
                                </option>
                            @endforeach

                            </select>
                        </div>

                        <!-- Filtrer par date -->
                        <div class="col-md-6 mb-3">
                            <input
                                type="date"
                                name="date"
                                class="form-control form-custom-user"
                                value="{{ request()->get('date') }}"
                            >
                        </div>

                        <!-- Filtrer par prix minimum -->
                        <div class="col-md-6 mb-3">
                            <input
                                type="number"
                                name="price"
                                class="form-control form-custom-user"
                                placeholder="{{ __('order.price_min') }}"
                                value="{{ request()->get('price') }}"
                                step="0.01"
                            >
                        </div>

                        <!-- Bouton -->
                        <div class="col-md-4 mb-3">
                            <button type="submit" class="btn view-cart">{{ __('order.search_button') }}</button>
                        </div>
                    </div>
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
                    <p>{{ __('order.no_orders') }}</p>
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
                                            ({{ $order->getTranslation('status', app()->getLocale()) }})
                                        </span>
                                    </p>




                        <!-- Afficher le bouton de modification seulement pour les statuts modifiables -->

                        @canany(['edit-commandes', 'edit-orders'])
                        @unless(in_array($order->getTranslation('status', 'en'), ['shipped', 'delivered', 'canceled']))
                            <a href="#" class="add_cart m-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $order->id }}">✏️</a>
                        @endunless
                            @endcanany

                                    @canany(['view-commandes', 'view-orders'])
                                    <a class="{{ Route::currentRouteName() === 'admin.commandes.show' ? 'active' : '' }}" href="{{ route('admin.commandes.show', $order->id) }}">👀</a>
                                    @endcanany
                                            </p>

                                            <ul class="menu-item-products">
                                                @php
                                                    $productCount = $order->products->sum('pivot.quantity');
                                                @endphp

                        <strong>
                            {{ $order->code }}
                            {{ $productCount > 1 ? __('order.products') : __('order.product') }} : {{ $productCount }}
                        </strong>

                        {{--
                        <strong> {{ $order->code }}
                        {{ trans_choice(__('order.product'), $productCount, ['count' => $productCount]) }}</strong>
                                        <strong> {{ $order->code }}  {{ \Illuminate\Support\Str::plural('Produit', $productCount) }} : {{ $productCount }}</strong> --}}

                                        {{-- @foreach($order->products as $product)
                                            <li>{{ $product->name }} (x{{ $product->pivot->quantity }})</li>
                                        @endforeach --}}

                                        {{-- <strong class="compte"> Id compte : {{ $order->customer->first_name }} {{ $order->customer->last_name }}</strong> --}}
                                    </ul>

                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editModal{{ $order->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel{{ $order->id }}">
                                            {{ __('order.edit_order_title', ['code' => $order->code]) }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('admin.commandes.update', $order->id) }}">
                                            @csrf
                                            @method('PUT')
                                            {{-- {{ __('order.status.pending') }} --}}
                                            {{-- @dd($order->status)  --}}
                                            {{-- @dd(__('order.status.pending'));   --}}
                                            {{ __( $order->status) }}

                                            <div class="mb-3">
                                                <label for="status" class="form-label">{{ __('order.status_filter') }}</label>
                                                <select name="status" class="form-select">
                                                    @foreach (\App\Models\Order::STATUSES as $key => $label)
                                                        <option value="{{ $key }}"
                                                            {{ old('status', $order->status) == $key ? 'selected' : '' }}>
                                                            {{ __($label) }}
                                                        </option>
                                                    @endforeach
                                                </select>



                                                @error('status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <button type="submit" class="btn view-cart"> {{ __('order.update_button') }}</button>
                                        </form>
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
    <script src="{{ asset('assets/js/searchOrder.js') }}"></script>
@endpush

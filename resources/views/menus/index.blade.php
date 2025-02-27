@extends('layouts.master')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('menu.culinary_universe') }}</h1>
            <p>{{ __('menu.culinary_description') }}</p>
        </div>

    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/menuId.css') }}">
@endpush

@section('content')

    <div class="container my-5">
        <div class="search-wrapper">
            {{-- <div class="search-container">
                <form method="GET" action="{{ route('menus.index') }}" id="search-form">
                    <input
                        type="text"
                        id="search"
                        class="search-input"
                        name="search"
                        placeholder="Rechercher..."
                        value="{{ request()->get('search') }}"
                    >

                </form>
            </div> --}}


            <form method="GET" action="{{ route('menus.index') }}" id="search-form">
                <div class="row">
                    <!-- Recherche par nom ou description -->
                    <div class="col-md-3 mb-3">
                        <input
                            type="text"
                            id="search"
                            class="form-control form-custom-user"
                            name="search"
                            placeholder="{{ __('menu.search_name_placeholder') }}"
                            value="{{ request()->get('search') }}"
                        >
                    </div>

                    <!-- Recherche par prix -->
                    <div class="col-md-3 mb-3">
                        <input
                            type="text"
                            id="price"
                            class="form-control form-custom-user"
                            name="price"
                            placeholder="{{ __('menu.search_price_placeholder') }}"
                            value="{{ request()->get('price') }}"
                        >
                    </div>

                    <!-- Filtrer par statut -->
                    <div class="col-md-3 mb-3">
                        <select name="status" id="status" class="form-select form-custom-user">
                            <option value="">{{ __('menu.select_status') }}</option>
                            <option value="available" {{ request()->get('status') == 'available' ? 'selected' : '' }}>
                                {{ __('menu.available') }}
                            </option>
                            <option value="recommended" {{ request()->get('status') == 'recommended' ? 'selected' : '' }}>
                                {{ __('menu.recommended') }}
                            </option>
                            <option value="seasonal" {{ request()->get('status') == 'seasonal' ? 'selected' : '' }}>
                                {{ __('menu.seasonal') }}
                            </option>
                        </select>
                    </div>

                    <!-- Filtrer par catégorie -->
                    <div class="col-md-3 mb-3">
                        <select name="category_id" id="category_id" class="form-select form-custom-user">
                            <option value="">{{ __('menu.select_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request()->get('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>



        </div>


        <div class="row">
            @if(session('success'))
                <div class="alert alert-success" id="success-alert">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger" id="error-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


                <div class="col-md-9 col-lg-9 mb-4">
                    @foreach ($menus as $menu)
                    <div class="menu-item m-1 p-3">
                        <div class="menu-item-image">
                            <img src="{{ url('storage/' . $menu->image) }}" alt="{{ $menu->name }}">
                        </div>

                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <h3 class="menu-item-title">
                                    {{ $menu->name }}
                                </h3>
                                <span class="menu-badge">{{ $menu->getTranslatedStatus() }}</span>
                                <div class="menu-item-dots"></div>
                                <div class="menu-item-price">
                                    @if ($menu->price_choice === 'detailed')
                                    £ {{   $menu->price_half_litre }} {{ __('menu.half') }} |  £ {{ $menu->price_litre }} {{ __('menu.full') }}
                                    @elseif (!empty($menu->formatted_price))
                                    £ {{ $menu->price }}
                                    @endif
                                </div>
                            </div>
                            <p class="menu-item-description">
                                <span class="texte">
                                    {{-- {{ $menu->description }} --}}
                                    {!! nl2br(e($menu->description)) !!}
                                </span>
                                <a class="add_cart m-3" href="#" data-id="{{ $menu->id }}">🛒</a>
                                <span class="texte categories">{{ $menu->category->name }}</span>
                                <div class="col-md-4">
                                    @if ($menu->price_choice === 'detailed')
                                    <select class="form-select form-custom-user size-selector" data-id="{{ $menu->id }}">
                                        <option value="half_litre">{{ __('menu.half') }}</option>
                                        <option value="litre">{{ __('menu.full') }}</option>
                                    </select>
                                    @endif

                                </div>

                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="col-md-3 mb-3">
                    <div class="cart-container container-fixed">
                        <h3>{{ __('menu.title') }}</h3>
                        <hr>
                        <div id="cart-items">
                            @foreach ($cart as $id => $item)
                            <div class="cart-item">
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}">
                                <div class="cart-item-details">
                                    <p>{{ $item['name'] }}</p>
                                    <span>
                                        {{ $item['quantity'] }} × £
                                        {{ number_format($item['price'], 2) }}

                                        {{-- Calcul du total de cet article --}}
                                        = £ {{ number_format($item['price'] * $item['quantity'], 2) }}

                                        {{-- Affichage de la taille choisie --}}
                                        {{-- @if (!empty($item['size']))
                                            ({{ __('menu.' . $item['size']) }})
                                        @endif --}}
                                    </span>
                                </div>
                                <button class="remove-item" data-id="{{ $id }}">×</button>
                            </div>
                            @endforeach

                            <div class="cart-subtotal">
                                <p>{{ __('menu.subtotal') }}:</p>
                                <span id="cart-subtotal">£{{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>
                        <div class="cart-actions">
                            <form action="{{ route('cart.view') }}" method="get">
                                <button type="submit" class="view-cart">{{ __('menu.view_cart') }}</button>
                            </form>
                            <form action="{{ route('checkout.view') }}" method="get">
                                <button type="submit" class="checkout">{{ __('menu.checkout') }}</button>
                            </form>
                        </div>
                    </div>

                </div>


        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="pagination-container">
                    {{ $menus->links('vendor.pagination.custom') }}
                </div>
            </div>

            {{-- <div class="col-md-6 mb-3">
                <div class="cart-container">
                    <h3>Panier</h3>
                    <hr>
                    <div id="cart-items">
                        @foreach ($cart as $id => $item)
                            <div class="cart-item">
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}">
                                <div class="cart-item-details">
                                    <p>{{ $item['name'] }}
                                    </p>
                                    <span>
                                        {{ $item['quantity'] }} × £{{ number_format($item['price'], 2) }}
                                        @php
                                            // Calcul du total en fonction de la taille et de la quantité
                                            $sizeValue = $item['size'] === 'half_litre' ? 0.5 : 1;
                                            $totalSize = $sizeValue * $item['quantity'];
                                        @endphp
                                        = {{ $totalSize }} litre(s)
                                    </span>
                                </div>
                                <button class="remove-item" data-id="{{ $id }}">×</button>
                            </div>
                        @endforeach


                        <div class="cart-subtotal">
                            <p>Sous-total:</p>
                            <span id="cart-subtotal">£{{ $subtotal }}</span>
                        </div>
                    </div>
                    <div class="cart-actions">
                        <form action="{{ route('cart.view') }}" method="get">
                            <button type="submit" class="view-cart">Panier</button>
                        </form>
                        <form action="{{ route('checkout.view') }}" method="get">
                            <button type="submit" class="checkout">Commander</button>
                        </form>
                    </div>

                </div>
            </div> --}}

        </div>
    </div>
@endsection

@push('scriptsCart')
    {{-- <script src="{{ asset('assets/js/search.js') }}"></script> --}}
    <script src="{{ asset('assets/js/searchCategory.js') }}"></script>
    <script src="{{ asset('assets/js/menu.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryField = document.getElementById('category_id');
            const priceSelection = document.getElementById('price-selection');

            function togglePriceSelection() {
                const selectedCategory = categoryField.options[categoryField.selectedIndex].text.toLowerCase();
                if (selectedCategory.includes('boissons naturelles')) {
                    priceSelection.style.display = 'block';
                } else {
                    priceSelection.style.display = 'none';
                }
            }

            categoryField.addEventListener('change', togglePriceSelection);
            togglePriceSelection(); // Initialiser l'état
        });
    </script>
@endpush

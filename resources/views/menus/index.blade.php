@extends('layouts.master')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Notre univers culinaire !</h1>
            <p>Découvrez un menu soigneusement élaboré pour éveiller vos papilles et satisfaire toutes vos envies.</p>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/menuId.css') }}">
@endpush

@section('content')

    <div class="container my-5">
        <div class="search-wrapper">
            <div class="search-container">
                <form method="GET" action="{{ route('menus.index') }}" id="search-form">
                    <!-- Champ de recherche -->
                    <input
                        type="text"
                        id="search"
                        class="search-input"
                        name="search"
                        placeholder="Rechercher..."
                        value="{{ request()->get('search') }}"
                    >

                </form>
            </div>
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

            @foreach ($menus as $menu)
                <div class="col-md-3 col-lg-6 mb-4">
                    <div class="menu-item p-3">
                        <div class="menu-item-image">
                            <img src="{{ url('storage/' . $menu->image) }}" alt="{{ $menu->name }}">
                        </div>

                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <h3 class="menu-item-title">
                                    {{ $menu->name }}
                                </h3>
                                <span class="menu-badge mt-md-0">{{ $menu->getStatusOptions()[$menu->status] ?? 'Inconnu' }}</span>
                                <div class="menu-item-dots"></div>
                                <div class="menu-item-price">
                                    @if (!empty($menu->formatted_price_with_text))
                                        {{ $menu->formatted_price_with_text }}
                                    @elseif (!empty($menu->formatted_price))
                                        {{ $menu->formatted_price }}
                                    @endif
                                </div>
                            </div>
                            <p class="menu-item-description">
                                <span class="texte">{{ $menu->description }}</span>
                                <a class="add_cart m-3" href="#" data-id="{{ $menu->id }}">🛒</a>
                                <span class="texte categories">{{ $menu->category->name }}</span>
                                <div class="col-md-4">
                                    @if ($menu->category->slug === 'boissons-naturelles')
                                    <select class="form-select form-custom-user size-selector" data-id="{{ $menu->id }}">
                                        <option value="half_litre">Demi-litre</option>
                                        <option value="litre">Litre</option>
                                    </select>
                                @endif
                                </div>

                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="pagination-container">
                    {{ $menus->links('vendor.pagination.custom') }}
                </div>
            </div>

            <div class="col-md-6 mb-3">
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
            </div>

        </div>
    </div>
@endsection

@push('scriptsCart')
    <script src="{{ asset('assets/js/search.js') }}"></script>
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

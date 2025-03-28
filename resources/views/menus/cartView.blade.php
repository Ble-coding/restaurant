
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
    <link rel="stylesheet" href="./assets/css/menuCart.css">
@endpush

@section('content')

   <!-- resources/views/cartView.blade.php -->
    <div class="container my-5">
        <div class="cart-container">
            <!-- Message -->
            @if(session('success'))
                <div class="alert alert-success" id="success-alert">
                    {{ session('success') }}
                </div>
            @endif


            <div class="table-responsive">
                <table class="table table-dark text-center align-middle cart-table">
                    <thead>
                        <tr>
                            <th>{{ __('menu.title') }}</th>
                            <th>{{ __('menu.price') }}</th>
                            <th>{{ __('menu.quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart as $id => $item)
                            @php
                                $itemSubtotal = $item['price'] * $item['quantity'];
                                $totalSize = null;
                                $sizeLabel = '';

                                if ($item['price_choice'] === 'detailed') {
                                    $sizeValue = $item['size'] === 'half_litre' ? 0.5 : 1;
                                    $totalSize = $sizeValue * $item['quantity'];
                                    $sizeLabel = $item['size'] === 'half_litre' ? __('menu.half') : __('menu.full');
                                }
                            @endphp
                          <tr>
                            <td>
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" width="50" height="50">
                                {{ $item['name'] }}
                                {{-- @if (!empty($item['size']))
                                    <br>({{ __('menu.' . $item['size']) }})
                                @endif --}}
                            </td>
                            <td>£ {{ number_format($item['price'], 2) }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>£ {{ number_format($item['price'] * $item['quantity'], 2) }}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <div class="cart-totals mt-4">
                        <div class="totals-row d-flex justify-content-between">
                            <span>{{ __('menu.subtotal') }}</span>
                            <span id="cart-subtotal">£{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="totals-row d-flex justify-content-between mt-2">
                            <span>{{ __('menu.deposit') }} (50%)</span>
                            <span id="cart-final-total">£{{ number_format($subtotal * 0.5, 2) }}</span>
                        </div>
                        <div class="totals-row d-flex justify-content-between mt-2">
                            <span>Total</span>
                            <span id="cart-final-total">£{{ number_format($total, 2) }}</span>
                        </div>
                        <form action="{{ route('checkout.view') }}" method="get">
                            <button type="submit" class="btn-checkout btn-orange mt-3 w-100">
                                {{ __('menu.checkout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection



@push('scriptsCart')
<script src="{{ asset('assets/js/search.js') }}"></script>
<script>
    $(document).ready(function () {
        // Suppression d'un produit du panier
        $('.remove-from-cart').click(function () {
            const productId = $(this).data('id');
            $.post("{{ route('cart.remove') }}", {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            }, function (response) {
                if (response.status === 'success') {
                    location.reload(); // Recharge la page pour afficher les modifications
                }
            });
        });

        // Mise à jour de la quantité
        $('.update-quantity').on('change', function () {
            const productId = $(this).data('id');
            const quantity = $(this).val();

            $.post("{{ route('cart.add') }}", {
                product_id: productId,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            }, function (response) {
                if (response.status === 'success') {
                    location.reload(); // Recharge la page pour afficher les modifications
                }
            });
        });

        // Message de succès temporaire
        $("#success-alert").fadeTo(2000, 500).slideUp(500, function () {
            $("#success-alert").slideUp(500);
        });
    });

    $(document).ready(function () {
        // Ajouter un produit au panier
        $(document).on("click", ".add_cart", function (e) {
            e.preventDefault();
            let productId = $(this).data("id");

            $.ajax({
                url: "/cart/add",
                method: "POST",
                data: {
                    product_id: productId,
                    _token: $("meta[name='csrf-token']").attr("content"),
                },
                success: function (response) {
                    if (response.status === "success") {
                        // Afficher le message de succès dans une div
                        $('#success-alert').text(response.message).show();
                        setTimeout(function() {
                            $('#success-alert').fadeOut();
                        }, 3000); // Masquer l'alerte après 3 secondes

                        updateCartBadge(); // Mettre à jour la badge
                        loadCart();        // Recharger le panier
                    } else {
                        // Afficher le message d'erreur dans une div
                        $('#error-alert').text("Erreur lors de l'ajout au panier.").show();
                        setTimeout(function() {
                            $('#error-alert').fadeOut();
                        }, 3000); // Masquer l'alerte après 3 secondes
                    }
                },
                error: function () {
                    // Afficher le message d'erreur dans une div
                    $('#error-alert').text("Erreur lors de la requête.").show();
                    setTimeout(function() {
                        $('#error-alert').fadeOut();
                    }, 3000); // Masquer l'alerte après 3 secondes
                },
            });
        });

        // Supprimer un produit du panier
        $(document).on("click", ".remove-item", function (e) {
            e.preventDefault();
            let productId = $(this).data("id");

            $.ajax({
                url: "/cart/remove",
                method: "POST",
                data: {
                    product_id: productId,
                    _token: $("meta[name='csrf-token']").attr("content"),
                },
                success: function (response) {
                    if (response.status === "success") {
                        // Afficher le message de succès dans une div
                        $('#success-alert').text(response.message).show();
                        setTimeout(function() {
                            $('#success-alert').fadeOut();
                        }, 3000); // Masquer l'alerte après 3 secondes

                        updateCartBadge(); // Mettre à jour la badge
                        loadCart();        // Recharger le panier
                    } else {
                        // Afficher le message d'erreur dans une div
                        $('#error-alert').text("Erreur lors de la suppression du produit.").show();
                        setTimeout(function() {
                            $('#error-alert').fadeOut();
                        }, 3000); // Masquer l'alerte après 3 secondes
                    }
                },
                error: function () {
                    // Afficher le message d'erreur dans une div
                    $('#error-alert').text("Erreur lors de la requête.").show();
                    setTimeout(function() {
                        $('#error-alert').fadeOut();
                    }, 3000); // Masquer l'alerte après 3 secondes
                },
            });
        });

        // Fonction pour mettre à jour la badge
        function updateCartBadge() {
            $.ajax({
                url: "/cart/count", // Route pour récupérer le nombre d'articles
                method: "GET",
                success: function (response) {
                    $("#cart-badge").text(response.count); // Mettre à jour la badge
                },
                error: function () {
                    $('#error-alert').text("Erreur lors de la mise à jour de la badge.").show();
                    setTimeout(function() {
                        $('#error-alert').fadeOut();
                    }, 3000); // Masquer l'alerte après 3 secondes
                },
            });
        }

        // Fonction pour recharger le contenu du panier
        function loadCart() {
            $("#cart-items").load(location.href + " #cart-items"); // Recharge uniquement la section panier
        }

        // Charger la badge à la page initiale
        updateCartBadge();
    });
</script>
@endpush




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
    <link rel="stylesheet" href="./assets/css/menuCheckout.css">
@endpush

@section('content')

   <!-- resources/views/checkoutView.blade.php -->
   <div class="container my-5">
        <div class="checkout-container">
            <!-- Coupon -->
            <div class="coupon-box" onclick="toggleCouponSection()">
                Vous avez un coupon ? Cliquez ici pour entrer votre code
            </div>

            <div class="coupon-section" id="couponSection">
                <div class="d-flex gap-2 mt-3 mb-4">
                    <input type="text" name="coupon_code" class="form-control form-custom" placeholder="Code du coupon">
                    <button type="submit" class="btn btn-orange">Appliquer le coupon</button>
                </div>
            </div>


            <!-- Formulaire de facturation -->
            <div class="row">
                <div class="col-md-5">
                    <h4 class="mt-3 mb-3">Détails de facturation</h4>
                    <form action="{{ route('checkout.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="firstName" class="form-label">Prénom</label>
                            <input type="text" id="firstName" name="first_name" class="form-control form-custom" placeholder="Prénom">
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Nom</label>
                            <input type="text" id="lastName" name="last_name" class="form-control form-custom" placeholder="Nom">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" id="email" name="email" class="form-control form-custom" placeholder="Email">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="text" id="phone" name="phone" class="form-control form-custom" placeholder="Téléphone">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse</label>
                            <input type="text" id="address" name="address" class="form-control form-custom" placeholder="Rue et numéro">
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label">Ville</label>
                            <input type="text" id="city" name="city" class="form-control form-custom" placeholder="Ville">
                        </div>
                        <div class="mb-3">
                            <label for="zip" class="form-label">Code postal</label>
                            <input type="text" id="zip" name="zip" class="form-control form-custom" placeholder="Code postal">
                        </div>
                        <div class="mb-3">
                            <label for="orderNotes" class="form-label">Notes de commande (facultatif)</label>
                            <textarea id="orderNotes" name="order_notes" class="form-control form-custom" rows="5" placeholder="Notes sur la commande, par ex. instructions de livraison."></textarea>
                        </div>
                        <button type="submit" class="btn btn-orange mt-3">COMMANDER</button>
                    </form>
                </div>

               <!-- Résumé de la commande -->
                <div class="col-md-7">
                    <h4 class="mt-4">Votre commande</h4>
                    <table class="table table-dark">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $id => $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="product-img" style="width: 80px; height: auto;">
                                            <span>{{ $item['name'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>{{ number_format($item['price'] * $item['quantity'], 2) }} €</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Sous-total</th>
                                <th>{{ number_format($subtotal, 2) }} €</th>
                            </tr>
                            <tr>
                                <th colspan="2">Total</th>
                                <th>{{ number_format($total, 2) }} €</th>
                            </tr>
                        </tfoot>
                    </table>
                    <p class="footer-text mt-3">
                        Vos données personnelles seront utilisées pour traiter votre commande, améliorer votre expérience sur ce site et pour d'autres fins décrites dans notre <a href="#">politique de confidentialité</a>.
                    </p>
                    <img src="./assets/images/menu/images.jpg" class="reseaux" alt="reseaux">

                    <section id="faq">
                        <div class="container py-5">
                            <div class="section-title">
                                <h2>FAQs</h2>
                                <p class="faq">Découvrez les réponses aux questions les plus fréquentes concernant nos services.</p>
                            </div>
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">
                                    <div class="accordion" id="accordionExample">
                                        <!-- Question 1 -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOne">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    Quels sont vos modes de paiement ?
                                                </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    Nous acceptons les paiements en ligne (carte bancaire ou PayPal) et en espèces à la livraison.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Question 2 -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingTwo">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    Comment fonctionne le paiement ?
                                                </button>
                                            </h2>
                                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    Un acompte de 50% est requis à la commande. Le solde restant est à régler lors de la livraison.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Question 3 -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingThree">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                    Quels sont vos délais de livraison ?
                                                </button>
                                            </h2>
                                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    Nos livraisons sont effectuées dans un délai de 1 à 2 heures après la confirmation de la commande.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                            </div>
                        </div>
                    </section>

                </div>


            </div>
        </div>
    </div>

@endsection

@push('scriptstoggle')
  <script src="{{ asset('assets/js/toggleCouponSection.js') }}"></script>
@endpush


@push('scriptsCart')
<script>
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

@push('scriptsCheckout')
    <script>

    </script>
@endpush


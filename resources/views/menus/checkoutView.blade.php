
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
    <link rel="stylesheet"  href="{{ asset('assets/css/menuCheckout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/idTel.css') }}">
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
                    <form id="checkout-form"  action="{{ route('checkout.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="firstName" class="form-label">Prénom</label>
                            <input type="text" id="firstName" name="first_name" class="form-control form-custom @error('first_name') is-invalid @enderror" placeholder="Prénom" value="{{ old('first_name') }}">
                            @error('first_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lastName" class="form-label">Nom</label>
                            <input type="text" id="last_name" name="last_name" class="form-control form-custom @error('last_name') is-invalid @enderror" placeholder="Nom" value="{{ old('last_name')}}">
                            @error('last_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" id="email" name="email" class="form-control form-custom @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}">
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            {{-- <input type="text" id="phone" name="phone" class="form-control form-custom @error('phone') is-invalid @enderror" placeholder="Téléphone" value="{{ old('phone') }}"> --}}

                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                            class="form-control form-custom @error('phone') is-invalid @enderror"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            >

                            <input type="hidden" id="country_code" name="country_code">
                            @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse</label>
                            <input type="text" id="address" name="address" class="form-control form-custom @error('address') is-invalid @enderror" placeholder="Rue et numéro" value="{{ old('address') }}">
                            @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">Ville</label>
                            <input type="text" id="city" name="city" class="form-control form-custom @error('city') is-invalid @enderror" placeholder="Ville" value="{{ old('city') }}">
                            @error('city')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="zip" class="form-label">Code postal</label>
                            <input type="text" id="zip" name="zip" class="form-control form-custom @error('zip') is-invalid @enderror" placeholder="Code postal" value="{{ old('zip') }}">
                            @error('zip')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <!-- Nouvelle section : Zone desservie -->
                        <div class="mb-3">
                            <label for="zone_id" class="form-label">Zone de livraison</label>
                            <select id="zone_id" name="zone_id" class="form-control form-custom @error('zone_id') is-invalid @enderror">
                                <option value="">Choisissez votre zone</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zone_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    <!-- Mode de paiement -->
                    <div class="mb-3">
                        <label for="create_payment_id" class="form-label">Mode de paiement</label>
                        @foreach($payments as $payment)
                            <div class="form-check">
                                <input
                                    class="form-check-input payment-option"
                                    type="radio"
                                    id="payment_{{ $payment->id }}"
                                    name="payment_id"
                                    value="{{ $payment->id }}"
                                    data-payment-name="{{ $payment->name }}"
                                    {{ old('payment_id') == $payment->id ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_{{ $payment->id }}">
                                    {{ $payment->name }}
                                </label>
                            </div>
                        @endforeach
                        @error('payment_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <input type="hidden" id="payment_method" name="payment_method" class="form-control">

                    <!-- Conteneur Stripe -->
                    <div id="stripe_elements_container" style="display: none;">
                        <label for="card-element" class="form-label">Paiement Stripe</label>
                        <div id="card-element"></div>
                    </div>



                        <div class="mb-3">
                            <label for="orderNotes" class="form-label">Notes de commande (facultatif)</label>
                            <textarea id="orderNotes" name="order_notes" class="form-control form-custom @error('order_notes') is-invalid @enderror" rows="5" placeholder="Notes sur la commande, par ex. instructions de livraison.">{{ old('order_notes') }}</textarea>
                            @error('order_notes')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="alert alert-info mt-3">
                            Un acompte de <strong>50%</strong> est requis pour confirmer votre commande. Le solde sera réglé à la livraison.
                        </div>

                        <!-- Case à cocher pour conditions générales -->
                        <div class="form-check mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="terms"
                                name="terms"
                                value="1"
                                {{ old('terms') ? 'checked' : '' }}>
                            <label class="form-check-label" for="terms">
                                J'accepte les <a href="#" target="_blank">conditions générales</a> de vente.
                            </label>
                            @error('terms')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" id="submit-button" class="btn btn-orange mt-3">COMMANDER</button>
                    </form>
                </div>
               <!-- Résumé de la commande -->
               <div class="col-md-7">
                <h4 class="mt-4">Votre commande</h4>
                <table class="table table-dark mt-4">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cart as $id => $item)
                            @php
                                // Calcul de la valeur en litres en fonction de la taille sélectionnée
                                $sizeValue = $item['size'] === 'half_litre' ? 0.5 : 1;
                                $totalSize = $sizeValue * $item['quantity'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" style="width: 80px; height: auto;">
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    {{ $item['quantity'] }} ×
                                    @if ($item['size'] === 'half_litre')
                                        0.5L
                                    @else
                                        1L
                                    @endif
                                    = <strong>{{ $totalSize }} Litre(s)</strong>
                                </td>
                                <td>£{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Sous-total</th>
                            <th>£{{ number_format($subtotal, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="2">Acompte (50%)</th>
                            <th>£{{ number_format($subtotal * 0.5, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="2">Frais de livraison</th>
                            <th>£{{ number_format($shipping_cost, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="2">Total</th>
                            <th>£{{ number_format($total, 2) }}</th>
                        </tr>
                    </tfoot>

                </table>

                <img src="{{ asset('assets/images/menu/images.jpg') }}" class="reseaux" alt="reseaux">


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
                                     <!-- Question 1: Comment commander ? -->
                                     <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFour">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                Comment commander ?
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <ol class="ps-3">
                                                    <li>Parcourez notre menu et ajoutez vos plats et boissons préférés à votre panier.</li>
                                                    <li>Confirmez votre commande en payant 50% à l'avance (solde à la livraison).</li>
                                                    <li>Choisissez votre mode de paiement : en ligne ou en espèces.</li>
                                                    <li>Recevez votre commande directement chez vous !</li>
                                                </ol>
                                                <p class="mt-2"><strong>Livraison rapide à Londres !</strong></p>
                                                <ul>
                                                    <li>Livraison gratuite pour les commandes de £30 ou plus.</li>
                                                    <li><strong>Zones desservies :</strong> [Liste des zones locales].</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Question 2 -->
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

                                    <!-- Question 3 -->
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

                                    <!-- Question 4 -->
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
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
<script src="{{ asset('assets/js/global.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripeContainer = document.getElementById('stripe_elements_container');
        const paymentOptions = document.querySelectorAll('.payment-option');

        paymentOptions.forEach(option => {
            option.addEventListener('change', function () {
                // Vérifiez si la méthode de paiement est Stripe
                if (this.dataset.paymentName === 'Stripe') {
                    stripeContainer.style.display = 'block'; // Affiche le conteneur Stripe
                } else {
                    stripeContainer.style.display = 'none'; // Masque le conteneur Stripe
                }
            });
        });

        // Si un choix est pré-sélectionné au chargement de la page
        const selectedOption = document.querySelector('.payment-option:checked');
        if (selectedOption && selectedOption.dataset.paymentName === 'Stripe') {
            stripeContainer.style.display = 'block';
        }
    });
</script>
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stripeApiKey = "{{ $stripeGateway->api_key }}"; // Clé API Stripe
    const stripe = Stripe(stripeApiKey);
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        classes: {
            base: 'form-control my-2 rounded',
        }
    });
    cardElement.mount('#card-element');

    const cardButton = document.getElementById('submit-button');
    cardButton.addEventListener('click', async (e) => {
        e.preventDefault();

        const selectedPaymentOption = document.querySelector('.payment-option:checked');
        if (selectedPaymentOption && selectedPaymentOption.dataset.paymentName === 'Stripe') {
            try {
                // Créer le PaymentMethod avec Stripe
                const { paymentMethod, error } = await stripe.createPaymentMethod('card', cardElement);

                if (error) {
                    alert(error.message);
                    return;
                }

                // Ajouter l'ID de payment_method à un champ caché
                document.getElementById('payment_method').value = paymentMethod.id;

                // Soumettre le formulaire au backend
                document.getElementById('checkout-form').submit();
            } catch (err) {
                console.error("Erreur lors de la création du PaymentMethod", err);
                alert("Une erreur est survenue. Veuillez réessayer.");
            }
        } else {
            alert("Veuillez sélectionner une méthode de paiement valide.");
        }
    });
});
</script>


@endpush


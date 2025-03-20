
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
                    {{ __('menu.coupon_text') }}
                </div>

                <div class="coupon-section" id="couponSection">
                    <div class="d-flex gap-2 mt-3 mb-4">
                        <input type="text" name="coupon_code" class="form-control form-custom" placeholder="  {{ __('menu.coupon_placeholder') }}">
                        <button type="submit" class="btn btn-orange">  {{ __('menu.apply_coupon') }}</button>
                    </div>
                </div>


            <!-- Formulaire de facturation -->
            <div class="row">
                <div class="col-md-5">
                    <h4 class="mt-3 mb-3">{{ __('menu.billing_details') }}</h4>
                    <form id="checkout-form"  action="{{ route('checkout.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="firstName" class="form-label">{{ __('menu.first_name') }}</label>
                            <input type="text" id="firstName" name="first_name" class="form-control form-custom @error('first_name') is-invalid @enderror" placeholder="{{ __('menu.first_name') }}" value="{{ old('first_name') }}">
                            @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lastName" class="form-label">{{ __('menu.last_name') }}</label>
                            <input type="text" id="last_name" name="last_name" class="form-control form-custom @error('last_name') is-invalid @enderror" placeholder="{{ __('menu.last_name') }}" value="{{ old('last_name')}}">
                            @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('menu.email') }}</label>
                            <input type="email" id="email" name="email" class="form-control form-custom @error('email') is-invalid @enderror" placeholder="{{ __('menu.email') }}" value="{{ old('email') }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">{{ __('menu.phone') }}</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control form-custom @error('phone') is-invalid @enderror" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <input type="hidden" id="country_code" name="country_code">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">{{ __('menu.address') }}</label>
                            <input type="text" id="address" name="address" class="form-control form-custom @error('address') is-invalid @enderror" placeholder="{{ __('menu.address_placeholder') }}" value="{{ old('address') }}">
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">{{ __('menu.city') }}</label>
                            <input type="text" id="city" name="city" class="form-control form-custom @error('city') is-invalid @enderror" placeholder="{{ __('menu.city') }}" value="{{ old('city') }}">
                            @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="zip" class="form-label">{{ __('menu.zip_code') }}</label>
                            <input type="text" id="zip" name="zip" class="form-control form-custom @error('zip') is-invalid @enderror" placeholder="{{ __('menu.zip_code') }}" value="{{ old('zip') }}">
                            @error('zip')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nouvelle section : Zone desservie -->
                    <div class="mb-3">
                    <label for="zone_id" class="form-label">{{ __('menu.delivery_zone') }}</label>
                    <select id="zone_id" name="zone_id" class="form-control form-custom @error('zone_id') is-invalid @enderror">
                        <option value="">{{ __('menu.select_zone') }}</option>
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
                        <label for="create_payment_id" class="form-label">{{ __('menu.payment_method') }}</label>
                        @foreach($payments as $payment)
                            <div class="form-check">
                                <input class="form-check-input payment-option" type="radio" id="payment_{{ $payment->id }}" name="payment_id" value="{{ $payment->id }}" data-payment-name="{{ $payment->name }}" {{ old('payment_id') == $payment->id ? 'checked' : '' }}>
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
                            <!-- Conteneur Stripe -->
                    <div id="stripe_elements_container" style="display: none;">
                        <label for="card-element" class="form-label">{{ __('menu.stripe_payment') }}</label>
                        <div id="card-element"></div>
                    </div>



                    <div class="mb-3">
                        <label for="orderNotes" class="form-label">{{ __('menu.order_notes') }}</label>
                        <textarea id="orderNotes" name="order_notes" class="form-control form-custom @error('order_notes') is-invalid @enderror" rows="5" placeholder="{{ __('menu.order_notes_placeholder') }}">{{ old('order_notes') }}</textarea>
                        @error('order_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info mt-3">
                        {!! __('menu.deposit_notice') !!}

                    </div>

                      <!-- Case à cocher pour conditions générales -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                        <label class="form-check-label" for="terms">
                            {!! __('menu.terms_acceptance') !!}
                        </label>
                        @error('terms')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                        <button type="submit" id="submit-button" class="btn btn-orange mt-3">{{ __('menu.order_button') }}</button>
                    </form>
                </div>
               <!-- Résumé de la commande -->
               <div class="col-md-7">
                <h4 class="mt-4">{{ __('menu.order_summary_title') }}</h4>
                <table class="table table-dark mt-4">
                    <thead>
                        <tr>
                            <th>{{ __('menu.order_product') }}</th>
                            <th>{{ __('menu.order_quantity') }}</th>
                            <th>{{ __('menu.order_subtotal') }}</th>
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
                                <!-- Produit (image + nom) -->
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" style="width: 80px; height: auto;">
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                </td>

                                <!-- Prix × Quantité (+ Taille si detailed) -->
                                <td>
                                    {{ $item['quantity'] }} × £{{ number_format($item['price'], 2) }}
                                    {{--
                                    @if ($item['price_choice'] === 'detailed')
                                        ({{ $sizeLabel }})
                                        <br>
                                        = <strong>{{ $totalSize }} {{ __('menu.litre') }}</strong>
                                    @endif --}}
                                </td>

                                <!-- Sous-total -->
                                <td>£{{ number_format($itemSubtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">{{ __('menu.order_subtotal_footer') }}</th>
                            <th>£{{ number_format($subtotal, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="2">{{ __('menu.order_deposit') }}</th>
                            <th>£{{ number_format($subtotal * 0.5, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="2">{{ __('menu.order_shipping') }}</th>
                            <th>£{{ number_format($shipping_cost, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="2">{{ __('menu.order_total') }}</th>
                            <th>£{{ number_format($total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>


                <img src="{{ asset('assets/images/menu/images.jpg') }}" class="reseaux" alt="{{ __('menu.order_network_image_alt') }}">


                <section id="faq">
                    <div class="container py-5">
                        <div class="section-title">
                            <h2>{{ __('menu.faq_title') }}</h2>
                            <p class="faq">{{ __('menu.faq_description') }}</p>
                        </div>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <div class="accordion" id="accordionExample">
                                     <!-- Question 1: Comment commander ? -->
                                     <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFour">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                {{__('menu.faq_order')  }}
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <ol class="ps-3">
                                                    <li> {{__('menu.faq_order_details') }}</li>
                                                    <li>{{__('menu.faq_order_deposit') }}</li>
                                                    <li>{{__('menu.faq_order_payment_method') }}</li>
                                                    <li>{{__('menu.faq_order_delivery') }}</li>
                                                </ol>
                                                <p class="mt-2"><strong>{{__('menu.quick_delivery') }}</strong></p>
                                                <ul>
                                                    <li>{{__('menu.free_delivery') }}</li>
                                                    <li><strong>{{__('menu.delivery_zones') }} :</strong>{{__('menu.local_zones') }}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Question 2 -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                {{__('menu.faq_payment') }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{__('menu.faq_payment_details') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Question 3 -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                {{__('menu.faq_deposit')}}
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{__('menu.faq_deposit_details')}}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Question 4 -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                 {{__('menu.faq_delivery')}}
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{__('menu.faq_delivery_details')}}
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

<script src="https://js.stripe.com/v3/"></script>
<script>
   document.addEventListener('DOMContentLoaded', function () {
    const stripeApiKey = "{{ $stripeGateway->api_key }}";
    const stripe = Stripe(stripeApiKey);
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        classes: {
            base: 'form-control my-2 rounded',
        }
    });
    cardElement.mount('#card-element');

    const submitButton = document.getElementById('submit-button');
    const checkoutForm = document.getElementById('checkout-form');
    const paymentMethodInput = document.getElementById('payment_method');
    const stripeContainer = document.getElementById('stripe_elements_container');
    const paymentOptions = document.querySelectorAll('.payment-option');

    // Gestion de l'affichage du conteneur Stripe
    paymentOptions.forEach(option => {
        option.addEventListener('change', function () {
            if (this.dataset.paymentName === 'Stripe') {
                stripeContainer.style.display = 'block';
            } else {
                stripeContainer.style.display = 'none';
            }
        });
    });

    // Pré-sélection au chargement
    const selectedOption = document.querySelector('.payment-option:checked');
    if (selectedOption && selectedOption.dataset.paymentName === 'Stripe') {
        stripeContainer.style.display = 'block';
    }

    submitButton.addEventListener('click', async function (e) {
        const selectedPaymentOption = document.querySelector('.payment-option:checked');

        if (selectedPaymentOption) {
            const paymentName = selectedPaymentOption.dataset.paymentName;

            // Bloquer uniquement pour Stripe
            if (paymentName === 'Stripe') {
                e.preventDefault();
                try {
                    const { paymentMethod, error } = await stripe.createPaymentMethod('card', cardElement);

                    if (error) {
                        alert(error.message);
                        return;
                    }

                    paymentMethodInput.value = paymentMethod.id;
                    checkoutForm.submit(); // Soumission pour Stripe après la création du PaymentMethod
                } catch (err) {
                    console.error("Erreur lors de la création du PaymentMethod", err);
                    alert("Une erreur est survenue. Veuillez réessayer.");
                }
            }
        }
    });
});

</script>


@endpush


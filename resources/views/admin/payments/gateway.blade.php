
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Portail</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">


     <!-- Formulaire de recherche -->
     <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.gateways.index') }}" id="search-form">
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


            @foreach ($gateways as $gateway)
                {{-- @if (!$coupon->expires_at || $coupon->expires_at > now())  --}}
                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title">{{ $gateway->payment->name }}</h3>
                                    <div class="menu-item-dots"></div>
                                    <div class="menu-item-price">
                                        <span class="menu-badge api-key" title="{{ $gateway->api_key }}">
                                            API KEY ({{ Str::limit($gateway->api_key, 20, '...') }})
                                        </span>

                                    </div>
                                </div>
                                <p class="menu-item-description">
                                    <span class="texte">
                                        @if ($gateway->payment->name !== 'Stripe')
                                            <span class="menu-badge">SITE ID {{ $gateway->site_id }}</span>
                                        @endif

                                    </span>
                                    @can('edit-gateways')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $gateway->id }}">✏️</a>
                                    @endcan
                                    @can('delete-gateways')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $gateway->id }}">🗑️</a>
                                    @endcan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la modification -->
                    <div class="modal fade" id="editModal{{ $gateway->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $gateway->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('admin.gateways.update', $gateway->id) }}">
                                        @csrf
                                        @method('PUT')


                                        <div class="row">
                                               <!-- Champ API Key -->
                                        <div class="col-md-4 mb-3">
                                            <label for="api_key" class="form-label">API KEY</label>
                                            <input type="text"
                                                   class="form-control form-custom-user"
                                                   id="api_key"
                                                   name="api_key"
                                                   value="{{ old('api_key', $gateway->api_key) }}">
                                            @error('api_key')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <!-- Champ Site ID -->
                                        <div class="col-md-4 mb-3" id="update_site_id_container">
                                            <label for="update_site_id" class="form-label">SITE ID</label>
                                            <input type="text"
                                                class="form-control form-custom-user"
                                                id="update_site_id"
                                                name="site_id"
                                                value="{{ old('site_id', $gateway->site_id) }}">
                                            @error('site_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Sélect Payment ID -->
                                        <div class="col-md-4 mb-3">
                                            <label for="update_payment_id" class="form-label">Paiement</label>
                                            <select name="payment_id" id="update_payment_id" class="form-control">
                                                @foreach($payments as $payment)
                                                    <option value="{{ $payment->id }}" {{ old('payment_id', $gateway->payment_id) == $payment->id ? 'selected' : '' }}>
                                                        {{ $payment->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('payment_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <!-- Champ Secret Key -->
                                       <!-- Champ Secret Key -->
                        <div class="col-md-4 mb-3">
                            <label for="secret_key_gateway_{{ $gateway->id }}" class="form-label">SECRET KEY</label>
                            <input type="password"
                                   class="form-control form-custom-user"
                                   id="secret_key_gateway_{{ $gateway->id }}"
                                   name="secret_key"
                                   value="{{ old('secret_key', $gateway->secret_key) }}">
                            <button type="button"
                                    class="btn btn-outline-secondary toggle-visibility"
                                    onclick="togglePasswordVisibility('secret_key_gateway_{{ $gateway->id }}')">
                                <i class="bi bi-eye" id="toggle-secret_key_gateway_{{ $gateway->id }}"></i>
                            </button>
                            @error('secret_key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>



                                        </div>

                                        <!-- Bouton Soumettre -->
                                        <div class="mt-4 text-end">
                                            <button type="submit" class="btn view-cart">Mettre à jour</button>
                                        </div>
                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la suppression -->
                    <div class="modal fade" id="deleteModal{{ $gateway->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $gateway->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $gateway->id }}">Supprimer la passerelle de paiement</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Êtes-vous sûr de vouloir supprimer la passerelle <strong>{{ $gateway->id }}</strong> ?</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.gateways.destroy', $gateway->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- @endif  --}}
            @endforeach


        </div>


    <div class="row">
        <div class="col-md-6">
            <div class="pagination-container">
                {{ $gateways->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-gateways')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>Créer une passerelle de paiement</h3>
                    <hr>
                    <form method="POST" action="{{ route('admin.gateways.store') }}">
                        @csrf

                        <!-- Champ API Key -->
                        <div class="mb-3">
                            <input type="text"
                                   class="form-control form-custom-user me-2"
                                   name="api_key"
                                   id="api_key_input"
                                   placeholder="API KEY"
                                   value="{{ old('api_key') }}">
                            @error('api_key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3" id="create_site_id_container">
                            <label for="create_site_id" class="form-label">SITE ID</label>
                            <input type="text" class="form-control form-custom-user" id="create_site_id" name="site_id"
                                   value="{{ old('site_id') }}">
                            @error('site_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="create_payment_id" class="form-label">Paiement Type</label>
                            <select name="payment_id" id="create_payment_id" class="form-control
                            form-custom-user">
                                @foreach($payments as $payment)
                                    <option value="{{ $payment->id }}" {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                                        {{ $payment->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Champ Secret Key -->
                        <div class="mb-3">
                            <input type="password"
                                   class="form-control form-custom-user me-2"
                                   name="secret_key"
                                   placeholder="SECRET KEY">
                            @error('secret_key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Placeholder pour Stripe Elements -->
                    <div id="stripe_elements_container" style="display: none;">
                        <label for="card-element" class="form-label">Stripe Payment</label>
                        <div id="card-element"></div>
                    </div>


                        <!-- Bouton Soumettre -->
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">Soumettre</button>
                        </div>
                    </form>
                </div>

            </div>
        @endcan
    </div>
</div>

@endsection

@push('scripts')
     <script src="{{ asset('assets/js/search.js') }}"></script>
     {{-- <script>
     document.addEventListener('DOMContentLoaded', function () {
            // Fonction pour gérer la visibilité des champs
            const toggleFieldVisibility = (paymentSelect, siteIdContainer, stripeContainer, stripePaymentId) => {
                const selectedValue = paymentSelect.value;

                if (selectedValue === stripePaymentId) {
                    siteIdContainer.style.display = 'none';
                    stripeContainer.style.display = 'block';
                } else {
                    siteIdContainer.style.display = 'block';
                    stripeContainer.style.display = 'none';
                }
            };

            // Gérer le formulaire de création
            const createFormStripePaymentId = '{{ $payments->firstWhere("name", "Stripe")->id ?? "" }}'; // ID Stripe
            const createPaymentSelect = document.getElementById('create_payment_id');
            const createSiteIdContainer = document.getElementById('create_site_id_container');
            const createStripeContainer = document.getElementById('stripe_elements_container');

            if (createPaymentSelect) {
                toggleFieldVisibility(
                    createPaymentSelect,
                    createSiteIdContainer,
                    createStripeContainer,
                    createFormStripePaymentId
                );

                createPaymentSelect.addEventListener('change', () => {
                    toggleFieldVisibility(
                        createPaymentSelect,
                        createSiteIdContainer,
                        createStripeContainer,
                        createFormStripePaymentId
                    );
                });
            }

            // Gérer le formulaire de mise à jour (similaire au formulaire de création)
            const updateFormStripePaymentId = '{{ $payments->firstWhere("name", "Stripe")->id ?? "" }}'; // ID Stripe
            const updatePaymentSelect = document.getElementById('update_payment_id');
            const updateSiteIdContainer = document.getElementById('update_site_id_container');
            const updateStripeContainer = document.getElementById('update_stripe_elements_container');

            if (updatePaymentSelect) {
                toggleFieldVisibility(
                    updatePaymentSelect,
                    updateSiteIdContainer,
                    updateStripeContainer,
                    updateFormStripePaymentId
                );

                updatePaymentSelect.addEventListener('change', () => {
                    toggleFieldVisibility(
                        updatePaymentSelect,
                        updateSiteIdContainer,
                        updateStripeContainer,
                        updateFormStripePaymentId
                    );
                });
            }
        });

      </script>


<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripeApiKey = "{{ $stripeGateway->api_key ?? '' }}";
        if (!stripeApiKey) {
            console.error('Stripe API Key manquante');
            return;
        }

        const stripe = Stripe(stripeApiKey);
        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            classes: {
                base: 'form-control my-2 rounded',
            },
        });
        cardElement.mount('#card-element');

        const form = document.getElementById('checkout-form');
        const cardButton = document.getElementById('submit-button');

        cardButton.addEventListener('click', async (e) => {
            e.preventDefault();
            const { paymentMethod, error } = await stripe.createPaymentMethod('card', cardElement);

            if (error) {
                alert(error.message);
            } else {
                const paymentMethodInput = document.createElement('input');
                paymentMethodInput.type = 'hidden';
                paymentMethodInput.name = 'payment_method';
                paymentMethodInput.value = paymentMethod.id;
                form.appendChild(paymentMethodInput);

                form.submit();
            }
        });
    });
</script> --}}

     <script src="{{ asset('assets/js/global.js') }}"></script>
@endpush



@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('paymentGateway.title') }}</h1>
            <p>{{ __('paymentGateway.dashboard_message') }}</p>
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
                    placeholder="{{ __('paymentGateway.search_placeholder') }}"
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
                                            {{ __('paymentGateway.api_key', ['key' => Str::limit($gateway->api_key, 20, '...')]) }}
                                        </span>

                                    </div>
                                </div>
                                <p class="menu-item-description">
                                    <span class="texte">
                                        @if ($gateway->payment->name !== 'Stripe')
                                            <span class="menu-badge">{{ __('paymentGateway.site_id', ['id' => $gateway->site_id]) }}</span>
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
                                            <label for="api_key" class="form-label">{{ __('paymentGateway.label_api_key') }}</label>
                                            <input type="text"
                                                   class="form-control form-custom-user"
                                                   id="api_key"
                                                   name="api_key"
                                                   value="{{ old('api_key', $gateway->api_key) }}">
                                            @error('api_key')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @if ($gateway->payment->name !== 'Stripe')
                                          <!-- Champ Site ID -->
                                          <div class="col-md-4 mb-3" id="update_site_id_container">
                                            <label for="update_site_id" class="form-label">{{ __('paymentGateway.label_site_id') }}</label>
                                            <input type="text"
                                                class="form-control form-custom-user"
                                                id="update_site_id"
                                                name="site_id"
                                                value="{{ old('site_id', $gateway->site_id) }}">
                                            @error('site_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        @endif



                                        <!-- Sélect Payment ID -->
                                        <div class="col-md-4 mb-3">
                                            <label for="update_payment_id" class="form-label">{{ __('paymentGateway.label_payment') }}</label>
                                            <select name="payment_id" id="update_payment_id" class="form-control   form-custom-user">
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
                        <div class="col-md-4 mb-3">
                            <label for="secret_key_gateway_{{ $gateway->id }}" class="form-label">{{ __('paymentGateway.label_secret_key') }} </label>
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
                                            <button type="submit" class="btn view-cart">{{ __('paymentGateway.update_button') }}</button>
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
                                    <h5 class="modal-title" id="deleteModalLabel{{ $gateway->id }}">{{ __('paymentGateway.delete_title') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ __('paymentGateway.delete_confirmation', ['id' => $gateway->payment->name]) }}</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.gateways.destroy', $gateway->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('paymentGateway.cancel') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ __('paymentGateway.confirm_delete') }}</button>
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
                    <h3>
                        {{ __('paymentGateway.create_title') }}
                        {{-- Créer une passerelle de paiement --}}

                    </h3>
                    <hr>
                    <form method="POST" action="{{ route('admin.gateways.store') }}">
                        @csrf

                        <!-- Champ API Key -->
                        <div class="mb-3">
                            <input type="text"
                                   class="form-control form-custom-user me-2"
                                   name="api_key"
                                   id="api_key_input"
                                   placeholder="{{ __('paymentGateway.label_api_key') }}"
                                   value="{{ old('api_key') }}">
                            @error('api_key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3" id="create_site_id_container">
                            <label for="create_site_id" class="form-label">{{ __('paymentGateway.label_site_id') }}</label>
                            <input type="text" class="form-control form-custom-user" id="create_site_id" name="site_id"
                                   value="{{ old('site_id') }}">
                            @error('site_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="create_payment_id" class="form-label">{{ __('paymentGateway.label_payment') }}</label>
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
                                   placeholder="{{ __('paymentGateway.label_secret_key') }}">
                            @error('secret_key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Placeholder pour Stripe Elements -->
                    <div id="stripe_elements_container" style="display: none;">
                        <label for="card-element" class="form-label">
                            {{ __('paymentGateway.stripe_payment') }}</label>
                        <div id="card-element"></div>
                    </div>


                        <!-- Bouton Soumettre -->
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart"> {{ __('paymentGateway.submit_button') }}</button>
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

     <script src="{{ asset('assets/js/global.js') }}"></script>
@endpush


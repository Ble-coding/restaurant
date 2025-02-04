
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('payment.title') }}</h1>
            <p>{{ __('payment.dashboard_message') }}</p>
        </div>

    </div>
@endsection

@section('content')

<div class="container my-5">


     <!-- Formulaire de recherche -->
     <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.payments.index') }}" id="search-form">
                <input
                    type="text"
                    id="search"
                    class="search-input"
                    name="search"
                    placeholder="{{ __('payment.search_placeholder') }}"
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


            @foreach ($payments as $payment)
                {{-- @if (!$coupon->expires_at || $coupon->expires_at > now())  --}}
                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title">{{ $payment->name }}</h3>
                                    <div class="menu-item-dots"></div>
                                    <div class="menu-item-price">
                                        {{-- <span class="menu-badge">{{ $coupon->discount }}% ({{ $coupon->translated_type }})</span> --}}
                                    </div>
                                </div>
                                <p class="menu-item-description">
                                    {{-- <span class="texte">
                                        @if ($coupon->expires_at && $coupon->expires_at < now())
                                            {{ $coupon->formatted_expires_at }}
                                        @else
                                            @if ($coupon->status === 'active')
                                                <span class="menu-badge">Actif</span>
                                            @else
                                                <span class="menu-badge">Inactif</span>
                                            @endif
                                        @endif
                                    </span> --}}
                                    @can('edit-payments')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $payment->id }}">✏️</a>
                                    @endcan
                                    @can('delete-payments')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $payment->id }}">🗑️</a>
                                    @endcan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la modification -->
                    <div class="modal fade" id="editModal{{ $payment->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $payment->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('admin.payments.update', $payment->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <!-- Première ligne -->
                                        {{-- <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="payment" class="form-label">{{ __('payment.label') }}</label>
                                                <input type="text" class="form-control form-custom-user" name="name" value="{{ old('name', $payment->name) }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="mb-3">
                                            <label for="name_fr" class="form-label">{{ __('payment.label_fr') }}</label>
                                            <input type="text" class="form-control form-custom-user" name="name_fr" value="{{ old('name_fr', $payment->getTranslation('name', 'fr')) }}">
                                            @error('name_fr')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Nom en Anglais -->
                                        <div class="mb-3">
                                            <label for="name_en" class="form-label">{{ __('payment.label_en') }}</label>
                                            <input type="text" class="form-control form-custom-user" name="name_en" value="{{ old('name_en', $payment->getTranslation('name', 'en')) }}">
                                            @error('name_en')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Bouton -->
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn view-cart">{{ __('payment.update_button') }}</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la suppression -->
                    <div class="modal fade" id="deleteModal{{ $payment->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $payment->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $payment->id }}">{{ __('payment.delete_payment') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ __('payment.delete_confirmation', ['name' => $payment->name]) }}</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.payments.destroy', $payment->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('payment.cancel') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ __('payment.confirm_delete') }} </button>
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
                {{ $payments->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-payments')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>{{ __('payment.create_payment') }}</h3>
                    <hr>
                    <form method="POST" action="{{ route('admin.payments.store') }}">
                        @csrf

                        <!-- Champ name -->
                        {{-- <div class="mb-3">
                            <label for="name" class="form-label">{{ __('payment.label') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name" placeholder="{{ __('payment.label') }}" value="{{ old('name') }}" >
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <!-- Nom en Français -->
                        <div class="mb-3">
                            <label for="name_fr" class="form-label">{{ __('payment.label_fr') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name_fr" placeholder="{{ __('payment.label_fr') }}" value="{{ old('name_fr') }}" required>
                            @error('name_fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nom en Anglais -->
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ __('payment.label_en') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name_en" placeholder="{{ __('payment.label_en') }}" value="{{ old('name_en') }}" required>
                            @error('name_en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                            <!-- Bouton Soumettre -->
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">{{ __('payment.submit_button') }}</button>
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


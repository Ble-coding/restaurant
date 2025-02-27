
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('coupon.title') }}</h1>
            <p>{{ __('coupon.dashboard_message') }}</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">

    {{-- <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.coupons.index') }}" id="search-form">
                <input
                    type="text"
                    id="search"
                    class="search-input"
                    name="search"
                    placeholder="Rechercher par nom..."
                    value="{{ request()->get('search') }}"
                >
            </form>
        </div>
    </div> --}}

     <!-- Formulaire de recherche -->
     <div class="search-wrapper">
        {{-- <div class="search-container"> --}}
            <form method="GET" action="{{ route('admin.coupons.index') }}" id="search-form">
                <div class="row">
                    <!-- Recherche par mot-clé -->
                    <div class="col-md-6 mb-3">
                        <input
                            type="text"
                            id="search"
                            class="form-control form-custom-user"
                            name="search"
                            placeholder="{{ __('coupon.search_placeholder') }}."
                            value="{{ request()->get('search') }}"
                        >
                    </div>

                    <!-- Filtrer par état (actif/expiré) -->
                    <div class="col-md-4 mb-3">
                        <select name="expired" id="expired" class="form-select form-custom-user">
                            <option value="">{{ __('coupon.status_filter') }}</option>
                            <option value="active" {{ request()->get('expired') === 'active' ? 'selected' : '' }}>{{ __('coupon.active') }}</option>
                            <option value="expired" {{ request()->get('expired') === 'expired' ? 'selected' : '' }}>{{ __('coupon.expired') }}</option>
                        </select>
                    </div>

                    <!-- Bouton de soumission -->
                    {{-- <div class="col-md-2 mb-3 text-end">
                        <button type="submit" class="btn view-cart">{{ __('coupon.search_button') }}</button>
                    </div> --}}
                </div>
            </form>
        {{-- </div> --}}
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


            @foreach ($coupons as $coupon)
                {{-- @if (!$coupon->expires_at || $coupon->expires_at > now())  --}}
                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title">{{ $coupon->code }}</h3>
                                    <div class="menu-item-dots"></div>
                                    <div class="menu-item-price">
                                        <span class="menu-badge">{{ $coupon->discount }}% ({{ $coupon->type }})</span>
                                    </div>
                                </div>
                                <p class="menu-item-description">
                                    <span class="texte">
                                        @if ($coupon->expires_at && $coupon->expires_at < now())
                                            {{ $coupon->formatted_expires_at }}
                                        @else
                                            @if ($coupon->status === 'active')
                                                <span class="menu-badge">{{ __('coupon.active_status') }}</span>
                                            @else
                                                <span class="menu-badge">{{ __('coupon.inactive_status') }}</span>
                                            @endif
                                        @endif
                                    </span>
                                    @can('edit-coupons')
                                     <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $coupon->id }}">✏️</a>
                                    @endcan
                                    @can('delete-coupons')
                                     <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $coupon->id }}">🗑️</a>
                                    @endcan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la modification -->
                    <div class="modal fade" id="editModal{{ $coupon->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $coupon->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    {{-- <h5 class="modal-title" id="editModalLabel{{ $coupon->id }}">Modifier le Coupon : {{ $coupon->code }}</h5> --}}
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <!-- Première ligne -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="code" class="form-label">{{ __('coupon.code') }}</label>
                                                <input type="text" class="form-control form-custom-user" name="code" value="{{ old('code', $coupon->code) }}">
                                                @error('code')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="discount" class="form-label">{{ __('coupon.discount') }}</label>
                                                <input type="number" class="form-control form-custom-user" name="discount" value="{{ old('discount', $coupon->discount) }}">
                                                @error('discount')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Deuxième ligne -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="type" class="form-label">{{ __('coupon.type') }}</label>
                                                <select name="type" class="form-select form-custom-user">
                                                    <option value="percent" {{ $coupon->type === 'percent' ? 'selected' : '' }}>{{ __('coupon.type_percent') }}</option>
                                                    <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>{{ __('coupon.type_fixed') }}</option>
                                                </select>
                                                @error('type')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="expires_at" class="form-label">{{ __('coupon.expiration_date') }}</label>
                                                <input type="date" class="form-control form-custom-user" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}">
                                            </div>
                                        </div>



                                        <!-- Bouton -->
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn view-cart">{{ __('coupon.update_coupon') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la suppression -->
                    <div class="modal fade" id="deleteModal{{ $coupon->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $coupon->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $coupon->id }}">{{ __('coupon.delete_coupon') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ __('coupon.delete_confirmation', ['code' => $coupon->code]) }}</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('coupon.cancel') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ __('coupon.confirm_delete') }}</button>
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
                {{ $coupons->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-coupons')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>{{ __('coupon.create_coupon') }}</h3>
                    <hr>
                    <form method="POST" action="{{ route('admin.coupons.store') }}">
                        @csrf

                        <!-- Champ Code -->
                        <div class="mb-3">
                            <label for="code" class="form-label">{{ __('coupon.code') }}</label>
                            <input type="text" class="form-control form-custom-user" name="code" placeholder="{{ __('coupon.code') }}" value="{{ old('code') }}" >
                            @error('code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ Réduction -->
                        <div class="mb-3">
                            <label for="discount" class="form-label">{{ __('coupon.discount') }}</label>
                            <input type="number" class="form-control form-custom-user" name="discount" placeholder="{{ __('coupon.discount') }}" value="{{ old('discount') }}" >
                            @error('discount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">{{ __('coupon.type') }}</label>
                            <select name="type" class="form-select" >
                                <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>{{ __('coupon.type_percent') }}</option>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>{{ __('coupon.type_fixed') }}</option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ Date d'expiration -->
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">{{ __('coupon.expiration_date') }}</label>
                            <input type="date" class="form-control form-custom-user" name="expires_at" value="{{ __('coupon.expiration_date') }}">
                            @error('expires_at')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bouton Soumettre -->
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">{{ __('coupon.submit') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        @endcan
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/searchCoupon.js') }}"></script>
    <script>

        window.addEventListener('DOMContentLoaded', (event) => {
        let successAlert = document.getElementById('success-alert');
        if (successAlert) {
            console.log('Success alert found'); // Pour déboguer
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 5000);
        }

        let errorAlert = document.getElementById('error-alert');
        if (errorAlert) {
            console.log('Error alert found'); // Pour déboguer
            setTimeout(() => {
                errorAlert.style.display = 'none';
            }, 5000);
        }
     });
    </script>
@endpush


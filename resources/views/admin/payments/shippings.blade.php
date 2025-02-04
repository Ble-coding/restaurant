
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('shippings.title') }}</h1>
            <p>{{ __('shippings.dashboard_message') }}</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">


     <!-- Formulaire de recherche -->
     <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.shippings.index') }}" id="search-form">
                <input
                    type="text"
                    id="search"
                    class="search-input"
                    name="search"
                   placeholder="{{ __('shippings.search_placeholder') }}"
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


            @foreach ($shippings as $shipping)
                {{-- @if (!$coupon->expires_at || $coupon->expires_at > now())  --}}
                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    {{-- <h3 class="menu-item-title">{{ $shipping->name }}</h3> --}}
                                    <h3 class="menu-item-title">{{ $shipping->getTranslation('name', app()->getLocale()) }}</h3>
                                    {{-- <pre>{{ json_encode($shipping->getTranslations('name')) }}</pre> --}}


                                    <div class="menu-item-dots"></div>
                                    <div class="menu-item-price">
                                        <span class="menu-badge">£{{ $shipping->price }}</span>
                                    </div>
                                </div>
                                <p class="menu-item-description">
                                    @can('edit-shippings')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $shipping->id }}">✏️</a>
                                    @endcan
                                    @can('delete-shippings')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $shipping->id }}">🗑️</a>
                                    @endcan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la modification -->
                    <div class="modal fade" id="editModal{{ $shipping->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $shipping->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('admin.shippings.update', $shipping->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <!-- Première ligne -->
                                        <div class="row">
                                            <!-- Champ name FR -->
                                            <div class="col-md-4 mb-3">
                                                <label for="name_fr" class="form-label">{{ __('shippings.name_placeholder_fr') }}</label>
                                                <input type="text" class="form-control form-custom-user"
                                                       name="name[fr]"
                                                       value="{{ old('name.fr', $shipping->getTranslation('name', 'fr')) }}">
                                                @error('name.fr')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Champ name EN -->
                                            <div class="col-md-4 mb-3">
                                                <label for="name_en" class="form-label">{{ __('shippings.name_placeholder_en') }}</label>
                                                <input type="text" class="form-control form-custom-user"
                                                       name="name[en]"
                                                       value="{{ old('name.en', $shipping->getTranslation('name', 'en')) }}">
                                                @error('name.en')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Champ Prix -->
                                            <div class="col-md-4 mb-3">
                                                <label for="price" class="form-label">{{ __('shippings.price') }}</label>
                                                <input type="number" class="form-control form-custom-user"
                                                       name="price"
                                                       value="{{ old('price', $shipping->price) }}">
                                                @error('price')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <!-- Bouton -->
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                <button type="submit" class="btn view-cart">{{ __('shippings.update_button') }}</button>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la suppression -->
                    <div class="modal fade" id="deleteModal{{ $shipping->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $shipping->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $shipping->id }}">{{ __('shippings.delete_title') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{!! __('shippings.delete_confirmation', ['name' => $shipping->name]) !!}</p>

                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.shippings.destroy', $shipping->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('shippings.cancel_button') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ __('shippings.confirm_delete') }}</button>
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
                {{ $shippings->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-shippings')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>{{ __('shippings.create_title') }}</h3>
                    <hr>
                    <form method="POST" action="{{ route('admin.shippings.store') }}">
                        @csrf

                        <!-- Champ name -->
                        {{-- <div class="mb-3">
                            <label for="name" class="form-label">{{ __('shippings.name') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name" placeholder="{{ __('shippings.name_placeholder') }}" value="{{ old('name') }}" >
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                         <!-- Champ name FR -->
                        <div class="mb-3">
                            <label for="name_fr" class="form-label">{{ __('shippings.name_placeholder_fr') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name[fr]" placeholder="{{ __('shippings.name_placeholder_fr') }}" value="{{ old('name.fr') }}">
                            @error('name.fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ name EN -->
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ __('shippings.name_placeholder_en') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name[en]" placeholder="{{ __('shippings.name_placeholder_en') }}" value="{{ old('name.en') }}">
                            @error('name.en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('shippings.price') }}</label>
                            <input type="number" class="form-control form-custom-user" name="price" placeholder="{{ __('shippings.price_placeholder') }}" value="{{ old('price') }}" >
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Bouton Soumettre -->
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">{{ __('shippings.submit_button') }}</button>
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


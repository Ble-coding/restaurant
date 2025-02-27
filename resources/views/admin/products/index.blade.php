
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('product.menu_title') }}</h1>
            <p>{{ __('product.dashboard_welcome') }}</p>
        </div>
    </div> 
@endsection

@section('content')

<div class="container my-5">
    <div class="search-wrapper">
        {{-- <div class="search-container"> --}}
            {{-- <form method="GET" action="{{ route('admin.products.index') }}" id="search-form">
                <input
                    type="text"
                    id="search"
                    class="search-input"
                    name="search"
                    placeholder="Rechercher..."
                    value="{{ request()->get('search') }}"
                >
            </form> --}}
            <form method="GET" action="{{ route('admin.products.index') }}" id="search-form">
                <div class="row">
                    <!-- Recherche par nom ou description -->
                    <div class="col-md-3 mb-3">
                        <input
                            type="text"
                            id="search"
                            class="form-control form-custom-user"
                            name="search"
                            placeholder="{{ __('product.search_name_placeholder') }}"
                            value="{{ request()->get('search') }}"
                        >
                    </div>

                    <!-- Recherche par prix -->
                    <div class="col-md-3 mb-3">
                        <input
                            type="text"
                            id="price"
                            class="form-control form-custom-user"
                            name="price"
                            placeholder="{{ __('product.search_price_placeholder') }}"
                            value="{{ request()->get('price') }}"
                        >
                    </div>

                    <!-- Filtrer par statut -->
                    <div class="col-md-3 mb-3">
                        <select name="status" class="form-select form-custom-user">
                            <option value="">{{ __('product.select_status') }}</option>
                            @foreach(App\Models\Product::getStatusLabels() as $key => $label)
                                <option value="{{ $key }}" {{ request()->get('status') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <!-- Filtrer par catégorie -->
                    <div class="col-md-3 mb-3">
                        <select name="category_id" id="category_id" class="form-select form-custom-user">
                            <option value="">{{ __('product.select_category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request()->get('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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

            @foreach ($products as $product)
                <div class="col-md-3 col-lg-6 mb-4">
                    <div class="menu-item p-3">
                        <div class="menu-item-image">
                            <img src="{{ url('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        </div>

                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <h3 class="menu-item-title">{{ $product->name }}
                                </h3>
                                <span class="menu-badge">{{ $product->getTranslatedStatus() }}</span>
                                <div class="menu-item-dots"></div>
                                <div class="menu-item-price">


                                    @if ($product->price_choice === 'detailed')
                                    £ {{   $product->price_half_litre }} {{ __('product.half') }} |  £ {{ $product->price_litre }} {{ __('product.full') }}
                                    @elseif (!empty($product->formatted_price))
                                    £ {{ $product->price }}
                                    @endif
                                </div>
                            </div>
                            <p class="menu-item-description">
                                <span class="texte">
                                    {!! nl2br(e($product->description)) !!}
                                    {{-- {{ $product->description }} --}}
                                </span>

                                <!-- Bouton pour ouvrir le modal de modification -->
                                {{-- <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $product->id }}"></a> --}}
                                @canany(['edit-menus', 'edit-products'])
                                <a class=" add_cart {{ Route::currentRouteName() === 'admin.products.edit' ? 'active' : '' }}" href="{{ route('admin.products.edit', $product->id) }}">✏️</a>
                                @endcanany

                                @canany(['delete-menus', 'delete-products'])
                                <!-- Bouton pour ouvrir le modal de suppression -->
                                <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">🗑️</a>
                                @endcanany

                                <span class="texte categories">{{ $product->category->name }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Modal pour la suppression -->
                <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $product->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $product->id }}">{{ __('product.delete_modal_title') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>{{ __('product.delete_modal_body', ['name' => $product->name]) }}</p>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('product.button_cancel') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('product.button_delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>


    <div class="row">
        <div class="col-md-6">
            <div class="pagination-container">
                {{ $products->links('vendor.pagination.custom') }}
            </div>
        </div>
        @canany(['create-menus', 'create-products'])
        <div class="col-md-6">
            <div class="cart-container-width">
                <h3>{{ __('product.add_product_title') }}</h3>
                <hr>
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Champ Nom -->
                    {{-- <div class="mb-3">
                        <label for="name" class="form-label">Nom du produit</label>
                        <input type="text" class="form-control form-custom-user me-2" name="name" id="name" value="{{ old('name') }}" placeholder="Libellé" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> --}}

                    <!-- Champ Description -->
                    {{-- <div class="mb-3">
                        <label for="description" class="form-label">Description du produit</label>
                        <textarea class="form-control form-custom-user me-2" name="description" id="description" rows="3" placeholder="Description">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> --}}

                     <!-- Champ Nom FR -->
                    <div class="mb-3">
                        <label for="name_fr" class="form-label">{{ __('product.name_fr') }}</label>
                        <input type="text" class="form-control form-custom-user me-2" name="name[fr]" id="name_fr" value="{{ old('name.fr') }}" placeholder="{{ __('product.name_placeholder_fr') }}" required>
                        @error('name.fr')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Champ Nom EN -->
                    <div class="mb-3">
                        <label for="name_en" class="form-label">{{ __('product.name_en') }}</label>
                        <input type="text" class="form-control form-custom-user me-2" name="name[en]" id="name_en" value="{{ old('name.en') }}" placeholder="{{ __('product.name_placeholder_en') }}" required>
                        @error('name.en')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Champ Description FR -->
                    <div class="mb-3">
                        <label for="description_fr" class="form-label">{{ __('product.description_fr') }}</label>
                        <textarea class="form-control form-custom-user me-2" name="description[fr]" id="description_fr" rows="3" placeholder="{{ __('product.description_placeholder_fr') }}">{{ old('description.fr') }}</textarea>
                        @error('description.fr')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Champ Description EN -->
                    <div class="mb-3">
                        <label for="description_en" class="form-label">{{ __('product.description_en') }}</label>
                        <textarea class="form-control form-custom-user me-2" name="description[en]" id="description_en" rows="3" placeholder="{{ __('product.description_placeholder_en') }}">{{ old('description.en') }}</textarea>
                        @error('description.en')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- Sélect pour choisir le type de prix -->

                    <div class="mb-3">
                        <label for="price_choice" class="form-label">{{ __('product.label_choice') }}</label>
                        <select id="priceType" name="price_choice" class="form-select form-custom-user mb-3">
                            <option value="normal">{{ __('product.price_normal') }}</option>
                            <option value="detailed">{{ __('product.price_detailed') }}</option>
                        </select>
                    </div>

                    <!-- Conteneur Prix Normal -->
                    <div id="price-normal" style="display: block;">
                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('product.label_price') }}</label>
                            <input type="number" class="form-control form-custom-user" name="price" id="price"
                                step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}">
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Conteneur Prix Détaillé -->
                    <div id="price-detailed" style="display: none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="price_half_litre" class="form-label">{{ __('product.label_price_half_litre') }}</label>
                                <input type="number" class="form-control form-custom-user me-2" name="price_half_litre" id="price_half_litre"
                                    step="0.01" min="0" value="{{ old('price_half_litre', $product->price_half_litre ?? '') }}">
                                @error('price_half_litre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="price_litre" class="form-label">{{ __('product.label_price_litre') }}</label>
                                <input type="number" class="form-control form-custom-user me-2" name="price_litre" id="price_litre"
                                    step="0.01" min="0" value="{{ old('price_litre', $product->price_litre ?? '') }}">
                                @error('price_litre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>



                    <!-- Champ Image -->
                    <div class="mb-3">
                        <div class="custom-file-upload">
                            <label for="image" class="form-label">{{ __('product.label_product_image') }}</label>
                            <div class="input-group">
                                <input type="file" class="form-control
                                form-custom-user me-2 d-none" name="image" id="image" accept="image/*">
                                <input type="text" class="form-control form-custom-user" id="fileName" placeholder="{{ __('product.no_file_chosen') }}" readonly>
                                <button type="button" class="btn view-cart" onclick="document.getElementById('image').click()">{{ __('product.choose_file') }}</button>
                            </div>
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        {{-- <label for="image" class="form-label">{{ __('product.label_product_image') }}</label>
                        <input type="file" class="form-control  form-custom-user me-2 " name="image" id="image" accept="image/*">
                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror --}}
                    </div>



                    <!-- Catégorie -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">{{ __('product.label_category') }}</label>
                        <select name="category_id" id="category_id" class="form-select form-custom-user">
                            <option value="">{{ __('product.select_category_placeholder') }}</option>
                            @foreach ($categories as $category)
                                {{-- @php
                                    $slug = json_decode($category->slug, true)[app()->getLocale()] ?? '';
                                @endphp --}}
                                <option value="{{ $category->id }}"
                                    {{-- data-slug="{{ $slug }}" --}}
                                    {{ old('category_id', isset($product) ? $product->category_id : '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- Champ Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('product.label_product_status') }}</label>
                        <select id="status" name="status" class="form-select" required>
                            @foreach(App\Models\Product::getStatusLabels() as $key => $label)
                                <option value="{{ $key }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>


                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                         @enderror
                    </div>

                    <!-- Bouton Soumettre -->
                    <div class="cart-actions mt-4">
                        <button type="submit" class="view-cart">{{ __('product.button_submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
        @endcanany

    </div>
</div>

@endsection

@push('scripts')
        <script>
            document.getElementById('image').addEventListener('change', function(event) {
                const fileInput = event.target;
                const fileNameInput = document.getElementById('fileName');

                if (fileInput.files.length > 0) {
                    fileNameInput.value = fileInput.files[0].name;
                } else {
                    fileNameInput.value = "{{ __('product.no_file_chosen') }}";
                }
            });
        </script>

    <!-- Inclure le fichier JS de select2 -->
    {{-- <script src="{{ asset('assets/js/search.js') }}"></script> --}}
    <script src="{{ asset('assets/js/searchCategory.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/price_boissons.js') }}"></script>
     --}}

     <!-- Script pour gérer l'affichage dynamique -->
{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        const categorySelect = document.getElementById("category_id");
        const priceBoissons = document.getElementById("price-boissons");
        const priceNormal = document.getElementById("price-normal");

        function togglePriceFields() {
            let selectedOption = categorySelect.options[categorySelect.selectedIndex];
            let slug = selectedOption.getAttribute("data-slug") || "";

            if (slug.includes("boisson") || slug.includes("drink")) {
                priceBoissons.style.display = "block";
                priceNormal.style.display = "none";
            } else {
                priceBoissons.style.display = "none";
                priceNormal.style.display = "block";
            }
        }

        categorySelect.addEventListener("change", togglePriceFields);
        togglePriceFields(); // Exécuter au chargement pour afficher correctement le champ sélectionné
    });
</script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const priceTypeSelect = document.getElementById('priceType');
            const priceNormal = document.getElementById('price-normal');
            const priceDetailed = document.getElementById('price-detailed');

            // Écoute le changement de sélection
            priceTypeSelect.addEventListener('change', function() {
                if (this.value === 'normal') {
                    priceNormal.style.display = 'block';
                    priceDetailed.style.display = 'none';
                } else {
                    priceNormal.style.display = 'none';
                    priceDetailed.style.display = 'block';
                }
            });
        });
    </script>

    {{-- <script id="boissons-slugs" type="application/json">
        @json($slugsBoissons)
    </script> --}}
    <script id="boissons-slugs" type="application/json">
        {!! json_encode($slugsBoissons) !!}
    </script>
@endpush





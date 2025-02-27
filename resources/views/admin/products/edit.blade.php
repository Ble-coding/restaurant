
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('product.menu_edit') }}</h1>
            <p>{{ __('product.dashboard_welcome') }}</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">


    <div class="row">
        <div class="col-md-2">

        </div>
        <div class="col-md-8">
            <div class="cart-container-edit">
                <h3>{{ __('product.edit_product') }}</h3>
                <hr>
                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Ligne 1 : Nom, Prix et Statut -->
                    <div class="row">
                        <!-- Champ Nom -->
                        {{-- <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nom du produit</label>
                            <input type="text" class="form-control form-custom-user me-2" name="name" value="{{ old('name', $product->name) }}" placeholder="Nom du produit" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="col-md-4 mb-3">
                            <label for="name_fr" class="form-label">{{ __('product.name_fr') }}</label>
                            <input type="text" class="form-control form-custom-user me-2" name="name[fr]" id="name_fr" value="{{ old('name.fr', $product->getTranslation('name', 'fr')) }}" placeholder="{{ __('product.name_placeholder_fr') }}" required>
                            @error('name.fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ Nom EN -->
                        <div class="col-md-4 mb-3">
                            <label for="name_en" class="form-label">{{ __('product.name_en') }}</label>
                            <input type="text" class="form-control form-custom-user me-2" name="name[en]" id="name_en" value="{{ old('name.en', $product->getTranslation('name', 'en')) }}" placeholder="{{ __('product.name_placeholder_en') }}" required>
                            @error('name.en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ Catégorie -->
                        <div class="col-md-4 mb-3">
                            <label for="category_id" class="form-label">{{ __('product.label_category') }}</label>
                            <select name="category_id" id="category_id" class="form-select form-custom-user">
                                <option value="">{{ __('product.select_category_placeholder') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                            data-slug="{{ $category->slug }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->getTranslation('name', app()->getLocale()) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                       <!-- Sélect pour choisir le type de prix -->
                       <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="price_choice" class="form-label">{{ __('product.label_choice') }}</label>
                                <select id="priceType" name="price_choice" class="form-select form-custom-user mb-3">
                                    <option value="normal" {{ old('price_choice', ($product->price && !$product->price_half_litre && !$product->price_litre) ? 'normal' : '') === 'normal' ? 'selected' : '' }}>
                                        {{ __('product.price_normal') }}
                                    </option>
                                    <option value="detailed" {{ old('price_choice', ($product->price_half_litre || $product->price_litre) ? 'detailed' : '') === 'detailed' ? 'selected' : '' }}>
                                        {{ __('product.price_detailed') }}
                                    </option>
                                </select>
                            </div>
                            <div id="price-normal" style="display: block;">
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">{{ __('product.label_price') }}</label>
                                    <input type="number" class="form-control form-custom-user" name="price" id="price"
                                        step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}">
                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
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
                        </div>


                    <!-- Ligne 2 : Image et Description -->
                    <div class="row">
                        <!-- Champ Image -->
                        {{-- <div class="col-md-4 mb-3">
                            <label for="image" class="form-label">Image du produit</label>
                            <input type="file" class="form-control form-custom-user me-2" name="image" id="image" accept="image/*">
                            @if($product->image)
                                <div class="mt-2">
                                    <img src="{{ url('storage/' . $product->image) }}" alt="Image actuelle" style="max-width: 100px;">
                                </div>
                            @endif
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}
                        <div class="col-md-4 mb-3">
                            <label for="image" class="form-label">{{ __('product.product_image') }}</label>
                            <div class="input-group">
                                <!-- Champ caché pour l'upload -->
                                <input type="file" class="form-control d-none" name="image" id="image" accept="image/*">

                                <!-- Champ texte pour afficher le nom du fichier -->
                                <input type="text" class="form-control" id="fileName" placeholder="{{ __('product.no_file_chosen') }}" readonly>

                                <!-- Bouton pour ouvrir la fenêtre de sélection de fichier -->
                                <button type="button" class="btn view-cart" onclick="document.getElementById('image').click()">
                                    {{ __('product.choose_file') }}
                                </button>
                            </div>

                            <!-- Prévisualisation de l'image actuelle -->
                            @if($product->image)
                                <div class="mt-2">
                                    <img src="{{ url('storage/' . $product->image) }}" alt="{{ __('product.current_image') }}" style="max-width: 100px;">
                                </div>
                            @endif

                            <!-- Affichage des erreurs -->
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Champ Description -->
                        {{-- <div class="col-md-4 mb-3">
                            <label for="description" class="form-label">Description du produit</label>
                            <textarea rows="5"
                            class="form-control form-custom-user mt-2" name="description" placeholder="Description">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}


                        <div class="col-md-4 mb-3">
                            <label for="description_fr" class="form-label">{{ __('product.description_fr') }}</label>
                            <textarea rows="5" class="form-control form-custom-user mt-2" name="description[fr]" placeholder="{{ __('product.description_placeholder_fr') }}">{{ old('description.fr', $product->getTranslation('description', 'fr')) }}</textarea>
                            @error('description.fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ Description EN -->
                        <div class="col-md-4 mb-3">
                            <label for="description_en" class="form-label">{{ __('product.description_en') }}</label>
                            <textarea rows="5" class="form-control form-custom-user mt-2" name="description[en]" placeholder="{{ __('product.description_placeholder_en') }}">{{ old('description.en', $product->getTranslation('description', 'en')) }}</textarea>
                            @error('description.en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                          <!-- Champ Statut -->
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">{{ __('product.label_product_status') }}</label>
                            <select id="statusUpdate" name="status" class="form-select form-custom-user select-product" required>
                                @foreach(App\Models\Product::getStatusLabels() as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $product->status) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    @canany(['edit-menus', 'edit-products'])
                    <button type="submit" class="btn view-cart">{{ __('product.button_update') }}</button>
                    @endcanany

                </form>
            </div>
        </div>
        <div class="col-md-2">

        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const priceTypeSelect = document.getElementById('priceType');
            const priceNormal = document.getElementById('price-normal');
            const priceDetailed = document.getElementById('price-detailed');

            // Fonction pour mettre à jour l'affichage
            function updatePriceVisibility(value) {
                if (value === 'detailed') {
                    priceNormal.style.display = 'none';
                    priceDetailed.style.display = 'block';
                } else {
                    priceNormal.style.display = 'block';
                    priceDetailed.style.display = 'none';
                }
            }

            // Au chargement initial, on affiche correctement selon la valeur actuelle
            updatePriceVisibility(priceTypeSelect.value);

            // Écoute le changement de sélection
            priceTypeSelect.addEventListener('change', function() {
                updatePriceVisibility(this.value);
            });
        });
    </script>

    <!-- Inclure le fichier JS de select2 -->
    {{-- <script src="{{ asset('assets/js/price_boissons.js') }}"></script> --}}
    <script id="boissons-slugs" type="application/json">
        @json($slugsBoissons)
    </script>
@endpush


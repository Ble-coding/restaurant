
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Menu Edit</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
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
                <h3>Editer un produit</h3>
                <hr>
                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Ligne 1 : Nom, Prix et Statut -->
                    <div class="row">
                        <!-- Champ Nom -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nom du produit</label>
                            <input type="text" class="form-control form-custom-user me-2" name="name" value="{{ old('name', $product->name) }}" placeholder="Nom du produit" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ Catégorie -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Catégorie</label>
                            <select name="category_id" id="category_id" class="form-select form-custom-user">
                                <option value="">-- Sélectionnez une catégorie --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                            data-slug="{{ $category->slug }}"
                                            {{ old('category_id', isset($product) ? $product->category_id : '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div id="price-fields">
                        <div class="col-md-4 mb-3" id="price-normal">
                            <label for="price" class="form-label">Prix du Produit</label>
                            <input type="number" class="form-control form-custom-user me-2" name="price" id="price" step="0.01" min="0" value="{{ old('price', $product->price) }}">
                        </div>
                        <div id="price-boissons" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="price_half_litre" class="form-label">1/2 Litre</label>
                                    <input type="number" class="form-control form-custom-user me-2" name="price_half_litre" id="price_half_litre" step="0.01" min="0" value="{{ old('price_half_litre', $product->price_half_litre) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="price_litre" class="form-label">1 Litre</label>
                                    <input type="number" class="form-control form-custom-user me-2" name="price_litre" id="price_litre" step="0.01" min="0" value="{{ old('price_litre', $product->price_litre) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ligne 2 : Image et Description -->
                    <div class="row">
                        <!-- Champ Image -->
                        <div class="col-md-4 mb-3">
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
                        </div>

                        <!-- Champ Description -->
                        <div class="col-md-4 mb-3">
                            <label for="description" class="form-label">Description du produit</label>
                            <textarea rows="5"
                            class="form-control form-custom-user mt-2" name="description" placeholder="Description">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                          <!-- Champ Statut -->
                          <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Statut du produit</label>
                            <select id="statusUpdate" name="status" class="form-select  form-custom-user select-product" required>
                                <option value="available" {{ old('status', $product->status) == 'available' ? 'selected' : '' }}>Disponible</option>
                                <option value="recommended" {{ old('status', $product->status) == 'recommended' ? 'selected' : '' }}>Recommandé</option>
                                <option value="seasonal" {{ old('status', $product->status) == 'seasonal' ? 'selected' : '' }}>Saisonnier</option>
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    <button type="submit" class="btn view-cart">Mettre à jour</button>

                </form>
            </div>
        </div>
        <div class="col-md-2">

        </div>
    </div>
</div>

@endsection

@push('scripts')
    <!-- Inclure le fichier JS de select2 -->
    <script src="{{ asset('assets/js/price_boissons.js') }}"></script>
    <script id="boissons-slugs" type="application/json">
        @json($slugsBoissons)
    </script>
@endpush



@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Menu</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">
    <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.products.index') }}" id="search-form">
                <!-- Champ de recherche -->
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
                                <span class="menu-badge">{{ $product->getStatusOptions()[$product->status] ?? 'Inconnu' }}</span>
                                <div class="menu-item-dots"></div>
                                <div class="menu-item-price">
                                    @if (!empty($product->formatted_price_with_text))
                                        {{ $product->formatted_price_with_text }}
                                    @elseif (!empty($product->formatted_price))
                                        {{ $product->formatted_price }}
                                    @endif
                                </div>
                            </div>
                            <p class="menu-item-description">
                                <span class="texte">{{ $product->description }}</span>

                                <!-- Bouton pour ouvrir le modal de modification -->
                                {{-- <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $product->id }}"></a> --}}
                                <a class=" add_cart {{ Route::currentRouteName() === 'admin.products.edit' ? 'active' : '' }}" href="{{ route('admin.products.edit', $product->id) }}">✏️</a>

                                <!-- Bouton pour ouvrir le modal de suppression -->
                                <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">🗑️</a>

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
                                <h5 class="modal-title" id="deleteModalLabel{{ $product->id }}">Confirmer la suppression</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Êtes-vous sûr de vouloir supprimer le produit <strong>{{ $product->name }}</strong> ? Cette action est irréversible.</p>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
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
        <div class="col-md-6">
            <div class="cart-container-width">
                <h3>Ajouter un produit</h3>
                <hr>
                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Champ Nom -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du produit</label>
                        <input type="text" class="form-control form-custom-user me-2" name="name" id="name" value="{{ old('name') }}" placeholder="Libellé" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Champ Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description du produit</label>
                        <textarea class="form-control form-custom-user me-2" name="description" id="description" rows="3" placeholder="Description">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Champs Prix -->
                    <div id="price-fields">
                        <!-- Champ Prix Normal -->
                        <div class="mb-3" id="price-normal">
                            <label for="price" class="form-label">Prix du Produit</label>
                            <input type="number" class="form-control form-custom-user" name="price" id="price"
                                step="0.01" min="0"
                                placeholder="Prix du Produit">
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champs pour Boissons Naturelles -->
                        <div id="price-boissons" style="display: none;">
                            <!-- Prix pour 1/2 Litre ou 1 Tasse -->
                            <div class="mb-3">
                                <label for="price_half_litre" class="form-label">Prix pour un contenant de 1/2 litre ou une tasse standard</label>
                                <input type="number" class="form-control form-custom-user" name="price_half_litre" id="price_half_litre"
                                       step="0.01" min="0"
                                       value="{{ old('price_half_litre', isset($product) ? $product->price_half_litre : '') }}"
                                       placeholder="">
                                @error('price_half_litre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Prix pour 1 Litre ou 1 Paquet -->
                            <div class="mb-3">
                                <label for="price_litre" class="form-label">Prix pour un contenant de 1 litre ou un paquet standard</label>
                                <input type="number" class="form-control form-custom-user" name="price_litre" id="price_litre"
                                       step="0.01" min="0"
                                       value="{{ old('price_litre', isset($product) ? $product->price_litre : '') }}"
                                       placeholder="">
                                @error('price_litre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>


                    <!-- Champ Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Image du produit</label>
                        <input type="file" class="form-control  form-custom-user me-2 " name="image" id="image" accept="image/*">
                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div class="mb-3">
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

                    <!-- Champ Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut du produit</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                            <option value="recommended" {{ old('status') == 'recommended' ? 'selected' : '' }}>Recommandé</option>
                            <option value="seasonal" {{ old('status') == 'seasonal' ? 'selected' : '' }}>Saisonnier</option>
                            <!-- Ajouter d'autres statuts si nécessaire -->
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Bouton Soumettre -->
                    <div class="cart-actions mt-4">
                        <button type="submit" class="view-cart">Soumettre</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
    <!-- Inclure le fichier JS de select2 -->
    <script src="{{ asset('assets/js/search.js') }}"></script>
    <script src="{{ asset('assets/js/price_boissons.js') }}"></script>
    <script id="boissons-slugs" type="application/json">
        @json($slugsBoissons)
    </script>
@endpush


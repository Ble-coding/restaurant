
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
                                <h3 class="menu-item-title">{{ $product->name }}<span class="menu-badge">{{ $product->getStatusOptions()[$product->status] ?? 'Inconnu' }}</span>
                                </h3>
                                <div class="menu-item-dots"></div>
                                <div class="menu-item-price">
                                    @if (!empty($product->formatted_price))
                                        {{ $product->formatted_price }}
                                    @elseif (!empty($product->formatted_price_with_text))
                                        {{ $product->formatted_price_with_text }}
                                    @endif
                                </div>
                            </div>
                            <p class="menu-item-description">
                                <span class="texte">{{ $product->description }}</span>

                                <!-- Bouton pour ouvrir le modal de modification -->
                                <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $product->id }}">✏️</a>

                                <!-- Bouton pour ouvrir le modal de suppression -->
                                <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}">🗑️</a>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Modal pour la modification -->
                <div class="modal fade" id="editModal{{ $product->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $product->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                {{-- <h5 class="modal-title" id="editModalLabel{{ $product->id }}">Modification du produit : {{ $product->name }}</h5> --}}
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <!-- Ligne 1 : Nom, Prix et Statut -->
                                    <div class="row">
                                        <!-- Champ Nom -->
                                        <div class="col-md-4 mb-3">
                                            <label for="name" class="form-label">Nom du produit</label>
                                            <input type="text" class="form-control form-custom-user me-2" name="name" value="{{ old('name', $product->name) }}" placeholder="Nom du produit" required>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Champ Prix -->
                                        <div class="col-md-4 mb-3">
                                            <label for="price" class="form-label">Prix du produit</label>
                                            <input type="number" step="0.01" class="form-control mt-2" name="price" value="{{ old('price', $product->price) }}" placeholder="Prix" required>
                                            @error('price')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Champ Statut -->
                                        <div class="col-md-4 mb-3">
                                            <label for="status" class="form-label">Statut du produit</label>
                                            <select id="statusUpdate" name="status" class="form-select select-product" required>
                                                <option value="available" {{ old('status', $product->status) == 'available' ? 'selected' : '' }}>Disponible</option>
                                                <option value="recommended" {{ old('status', $product->status) == 'recommended' ? 'selected' : '' }}>Recommandé</option>
                                                <option value="seasonal" {{ old('status', $product->status) == 'seasonal' ? 'selected' : '' }}>Saisonnier</option>
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Ligne 2 : Image et Description -->
                                    <div class="row">
                                        <!-- Champ Image -->
                                        <div class="col-md-6 mb-3">
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
                                        <div class="col-md-6 mb-3">
                                            <label for="description" class="form-label">Description du produit</label>
                                            <textarea class="form-control mt-2" name="description" placeholder="Description">{{ old('description', $product->description) }}</textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Bouton Soumettre -->
                                    <div class="cart-actions mt-4">
                                        <button type="submit" class="view-cart">Modifier</button>
                                    </div>
                                </form>
                            </div>
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

                    <!-- Champ Prix -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Prix du produit</label>
                        <input type="number" class="form-control form-custom-user me-2" name="price" id="price"
                               step="0.01" min="0"
                               value="{{ old('price', isset($product) ? $product->price : '') }}"
                               placeholder="Prix" required>
                        @error('price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- Champ Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Image du produit</label>
                        <input type="file" class="form-control  form-custom-user me-2 " name="image" id="image" accept="image/*">
                        @error('image')
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
    <script>

        // Initialiser select2 avec un z-index personnalisé
        $('.select-product').select2({
            dropdownParent: $('#editModal{{ $product->id }}'), // Limite le dropdown au modal
            width: '100%', // S'assure que le dropdown s'aligne bien avec l'input
        });;
    </script>

@endpush



@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Catégorie</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">

    <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.categories.index') }}">
                @csrf
                <input
                    type="text"
                    class="search-input"
                    name="search"
                    placeholder="Rechercher......"
                    value="{{ request()->get('search') }}"
                    oninput="this.form.submit()"
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


            @foreach ($categories as $category)

                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title">{{ $category->name }}</h3>
                                    <div class="menu-item-dots"></div>
                                </div>
                                <p class="menu-item-description">
                                    <span class="texte">
                                    <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">✏️</a>
                                    <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $category->id }}">🗑️</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la modification -->
                    <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $category->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $category->id }}">Modifier la catégorie : {{ $category->code }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Libellé</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', $category->name) }}">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn view-cart">Mettre à jour</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la suppression -->
                    <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $category->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $category->id }}">Supprimer la catégorie</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $category->name }}</strong> ?</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}">
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
                {{ $categories->links('vendor.pagination.custom') }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="cart-container-width">
                <h3>Créer une catégorie de blog</h3>
                <hr>
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf

                    <!-- Champ Code -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Catégorie</label>
                        <input type="text" class="form-control form-custom-user" name="name" placeholder="Libellé" value="{{ old('name') }}">
                        @error('name')
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


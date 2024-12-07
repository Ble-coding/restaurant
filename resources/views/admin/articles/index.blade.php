
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Blogs</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">

    <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.articles.index') }}">
                @csrf
                <input
                    type="text"
                    class="search-input"
                    name="search"
                    placeholder="Rechercher..."
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

            @foreach ($articles as $article)
            <div class="col-md-3 col-lg-6 mb-4">
                <div class="menu-item p-3">
                    <div class="menu-item-image">
                        <img src="{{ url('storage/' . $article->image) }}" alt="{{ $article->title }}">
                    </div>

                    <div class="menu-item-content">
                        <div class="menu-item-header">
                            <h3 class="menu-item-title"> {{ $article->title }} </h3>
                            <div class="menu-item-dots"></div>
                            <div class="menu-item-price">
                                {{ $article->getTranslatedStatus() }}
                            </div>
                        </div>
                        <p class="menu-item-description">
                            <span class="texte">{{ Str::limit(strip_tags($article->content), 100) }}</span>
                            <a href="{{ route('admin.articles.edit', $article->id) }}" class="add_cart m-3">
                                ✏️
                            </a>

                            <!-- Bouton pour ouvrir le modal de suppression -->
                            <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $article->id }}">🗑️</a>
                            <span class="menu-badge">{{ $article->category->name }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal pour la suppression -->
            <div class="modal fade" id="deleteModal{{ $article->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $article->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $article->id }}">Confirmer la suppression</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer l'article <strong>{{ $article->title }}</strong> ? Cette action est irréversible.</p>
                        </div>
                        <div class="modal-footer">
                            <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}">
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
                {{ $articles->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <!-- Inclure le fichier JS de select2 -->
    {{-- <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> --}}

    {{-- <script>

        // Initialiser select2 avec un z-index personnalisé
        $('.select-product').select2({
            dropdownParent: $('#editModal{{ $product->id }}'), // Limite le dropdown au modal
            width: '100%', // S'assure que le dropdown s'aligne bien avec l'input
        });;
    </script> --}}

@endpush


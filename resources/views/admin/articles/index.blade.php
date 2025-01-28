
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
            <form method="GET" action="{{ route('admin.articles.index') }}" id="search-form">
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

            @foreach ($articles as $article)

              @php
                    $locale = app()->getLocale(); // Récupère la langue actuelle (fr ou en)
                    $title = $locale === 'fr' ? $article->title_fr : $article->title_en;
                    $content = $locale === 'fr' ? $article->content_fr : $article->content_en;
                @endphp
                    <div class="col-md-3 col-lg-6 mb-4">
                    <div class="menu-item p-3">

                            <div class="menu-item-image">
                                <img src="{{ url('storage/' . $article->image) }}" alt="{{ $article->title }}">
                            </div>

                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title"> {{ $title}} </h3>
                                    <div class="menu-item-dots"></div>
                                    <div class="menu-item-price">
                                        {{ $article->getTranslatedStatus() }}
                                    </div>
                                </div>
                                <p class="menu-item-description">
                                    <span class="texte">{{ Str::limit(strip_tags($content), 100) }}</span>

                                    @canany(['view-articles', 'view-blogs'])
                                    <a class="{{ Route::currentRouteName() === 'admin.articles.index' ? 'active' : '' }}" href="{{ route('admin.articles.show', $article->id) }}">👀
                                    </a>
                                    @endcanany
                                    @canany(['edit-articles', 'edit-blogs'])
                                    <a href="{{ route('admin.articles.edit', $article->id) }}" class="add_cart m-3">
                                        ✏️
                                    </a>
                                    @endcanany

                                    <!-- Bouton pour ouvrir le modal de suppression -->
                                    @canany(['delete-articles', 'delete-blogs'])
                                    <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $article->id }}">🗑️</a>
                                    @endcanany
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
    <script src="{{ asset('assets/js/search.js') }}"></script>
@endpush


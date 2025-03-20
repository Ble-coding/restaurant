
@extends('layouts.masterAdmin')



@push('styles')

    <link rel="stylesheet"  href="{{ asset('assets/css/menuSelect.css') }}">
@endpush
@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('blog.dashboard_title') }}</h1>
            <p>{{ __('blog.dashboard_welcome_message') }}
    </div>
@endsection

@section('content')




<div class="container my-5">

    <div class="search-wrapper">
        <div class="">
            <form method="GET" action="{{ route('admin.articles.index') }}" id="search-form">
                <div class="row">
                    <!-- Recherche par mot-clé -->
                    <div class="col-md-6 mb-3">
                        <input
                            type="text"
                            id="search"
                            class="form-control  form-custom-user"
                            name="search"
                            placeholder="{{ __('blog.search_placeholder') }}"
                            value="{{ request()->get('search') }}"
                        >
                    </div>
            
                    <!-- Filtrer par catégorie -->
                    <div class="col-md-6 mb-3">
                        <select name="category_id" class="form-select form-custom-user">
                            <option value="">{{ __('blog.category_filter') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request()->get('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- Filtrer par statut -->
                    <div class="col-md-6 mb-3">
                        <select name="status" class="form-select form-custom-user">
                            <option value="">{{ __('blog.status_filter') }}</option>
                            <option value="published" {{ request()->get('status') == 'published' ? 'selected' : '' }}>
                                {{ __('blog.published') }}
                            </option>
                            <option value="draft" {{ request()->get('status') == 'draft' ? 'selected' : '' }}>
                                {{ __('blog.draft') }}
                            </option>
                        </select>
                    </div>
            
                    <!-- Bouton de recherche -->
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn view-cart">{{ __('blog.search_button') }}</button>
                    </div>
                </div>
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
                                    <span class="menu-badge">
                                        {{ $article->category ? $article->category->getTranslation('name', app()->getLocale()) : __('messages.no_category') }}
                                    </span>

                                </p>
                            </div>

                    </div>
                </div>

                <!-- Modal pour la suppression -->
                <div class="modal fade" id="deleteModal{{ $article->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $article->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $article->id }}">{{ __('blog.delete_modal_title') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>{!! __('blog.delete_modal_body', ['title' => $article->title]) !!}</p>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('blog.delete_modal_cancel_button') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('blog.delete_modal_confirm_button') }}</button>
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


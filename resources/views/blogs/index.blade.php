@extends('layouts.master')

@push('styles')

<link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/menuId.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/idTel.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
@endpush


@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('blog.blogs.title') }}</h1>
            <p>{{ __('blog.blogs.description') }}</p>
        </div>
    </div>
@endsection

@section('content')
    <section id="blog">
        <div class="blog-section">
            <div class="container">
                <div class="section-title">
                    <h2>{{ __('home.blog_latest_news') }}</h2>
                    <p>{{ __('home.blog_description') }}</p>
                </div>

                <div class="search-wrapper">
                    <div class="    ">
                        <form method="GET" action="{{ route('blogs.index') }}" id="search-form">
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


                <div class="blog-grid">
                    @foreach ($blogs as $blog)
                    @php
                        $locale = app()->getLocale(); // Récupère la langue actuelle (fr ou en)
                        $title = $locale === 'fr' ? $blog->title_fr : $blog->title_en;
                        $content = $locale === 'fr' ? $blog->content_fr : $blog->content_en;

                        // Définir le format de la date en fonction de la langue
                        $dateFormat = $locale === 'fr' ? 'd F Y' : 'M d, Y';
                    @endphp
                        <article class="blog-item">
                            <a class="{{ Route::currentRouteName() === 'blogs.index' ? 'active' : '' }}" href="{{ route('blogs.show', $blog->id) }}">
                            <!-- Lien vers la page du blog -->
                                <div class="blog-cadre">
                                    <div class="blog-image">
                                        <!-- Image dynamique -->
                                        <img src="{{ url('storage/' . $blog->image) }}" alt="{{ $blog->title }}">
                                    </div>
                                </div>
                                <div class="blog-content">
                                    <!-- Date formatée -->
                                <span class="blog-date">{{ $blog->created_at->locale($locale)->translatedFormat($dateFormat) }}</span>

                                    <!-- Titre dynamique -->
                                    <h3 class="blog-title-semi">
                                        {{ $title}}
                                        {{-- {{ $blog->title }} --}}
                                        {{-- {{ Str::limit(strip_tags($blog->title), 20) }} --}}
                                    </h3>

                                    <!-- Extrait du contenu (limité à 100 caractères) -->
                                    <p class="blog-excerpt">
                                        {{-- {{ Str::limit(strip_tags($blog->content), 70) }} --}}
                                        {{ Str::limit(strip_tags($content), 70) }}
                                    </p>

                                </a>

                                @if(Auth::guard('customer')->check())
                                <div class="blog-meta">
                                    <span class="like-btn" data-post-id="{{ $blog->id }}" style="cursor: pointer;">
                                        <i class="bi {{ $blog->likes->contains('customer_id', auth('customer')->id()) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                        <span id="like-count-{{ $blog->id }}">
                                            {{ $blog->likes->count() }} {{ Str::plural('Like', $blog->likes->count()) }}
                                        </span>
                                    </span>
                                    <span><i class="bi bi-chat"></i>  {{ $blog->comments_count }} {{ Str::plural('commentaire', $blog->comments_count) }}</span>
                                </div>
                                @else
                                @endif


                                </div>

                        </article>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="pagination-container">
                            {{ $blogs->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="contenu-btn">

            </div> --}}

        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/like.js') }}"></script>
    <script src="{{ asset('assets/js/search.js') }}"></script>
@endpush


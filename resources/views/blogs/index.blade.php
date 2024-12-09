@extends('layouts.master')

@push('styles')

<link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/idTel.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
@endpush

@section('content')
    <section id="blog">
        <div class="blog-section">
            <div class="container">
                <div class="section-title">
                    <h2>Dernières nouvelles de notre blog</h2>
                    <p>  Découvrez l'univers culinaire africain grâce à nos articles inspirants. </p>
                </div>

                <div class="search-wrapper">
                    <div class="search-container">
                        <form method="GET" action="{{ route('blogs.index') }}" id="search-form">
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


                <div class="blog-grid">
                    @foreach ($blogs as $blog)
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
                                    <span class="blog-date">{{ $blog->created_at->format('d M Y') }}</span>

                                    <!-- Titre dynamique -->
                                    <h3 class="blog-title-semi">
                                        {{ $blog->title }}
                                        {{-- {{ Str::limit(strip_tags($blog->title), 20) }} --}}
                                    </h3>

                                    <!-- Extrait du contenu (limité à 100 caractères) -->
                                        <p class="blog-excerpt">
                                            {{ Str::limit(strip_tags($blog->content), 70) }}
                                        </p>
                                    </a>
                                        <!-- Métadonnées statiques (laisser pour l'instant) -->
                                        <div class="blog-meta">

                                            <span class="like-btn" data-post-id="{{ $blog->id }}" style="cursor: pointer;">
                                                <i class="bi {{ $blog->likes->contains('customer_id', auth('customer')->id()) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                                <span id="like-count-{{ $blog->id }}">
                                                    {{ $blog->likes->count() }} {{ Str::plural('Like', $blog->likes->count()) }}
                                                </span>
                                            </span>


                                            <span><i class="bi bi-chat"></i>  {{ $blog->comments_count }} {{ Str::plural('commentaire', $blog->comments_count) }}</span>
                                        </div>
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


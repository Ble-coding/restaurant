@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/accueil.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/footer.css') }}">
@endpush

@section('content')
<section id="blog">
    <div class="blog-section">
        <div class="container">
            <div class="section-title">
                <h2>Dernières nouvelles de notre blog</h2>
                <p>  Découvrez l'univers culinaire africain grâce à nos articles inspirants. </p>
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

                                    <!-- Métadonnées statiques (laisser pour l'instant) -->
                                    <div class="blog-meta">
                                        <span><i class="bi bi-heart"></i> 45 Likes</span>
                                        <span><i class="bi bi-chat"></i> 12 Commentaires</span>
                                    </div>
                                </div>
                            </a>
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



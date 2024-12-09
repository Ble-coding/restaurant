@extends('layouts.masterAdmin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/blogAdminId.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/idTel.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
@endpush

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Blog ID</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container my-5">
        <div class="row">

            <!-- Section principale (texte principal) -->
            <div class="col-md-8">
                <div class="content-section">
                    <!-- Catégorie du blog -->
                    <div class="blog-category">
                        {{ $article->category->name ?? 'Non catégorisé' }}
                    </div>

                    <!-- Titre du blog -->
                    <h1 class="blog-title">
                        {{ $article->title }}
                    </h1>

                    <!-- Métadonnées -->
                    <div class="blog-meta">
                        Publié le {{ $article->created_at->format('d M Y') }}  <i class="bi bi-chat"></i> {{ $commentsCount }} {{ Str::plural('commentaire', $commentsCount) }}
                            <span id="like-count-{{ $article->id }}">
                                <i class="bi bi-heart-fill"></i>
                                {{ $article->likes->count() }} {{ Str::plural('Like', $article->likes->count()) }}
                            </span>
                    </div>

                    <!-- Image principale -->
                    <div class="blog-image">
                        @if ($article->image)
                            <img src="{{ url('storage/' . $article->image) }}" alt="{{ $article->title }}" class="img-fluid">
                        @else
                            <img src="{{ asset('assets/images/default-blog.jpg') }}" alt="Image par défaut" class="img-fluid">
                        @endif
                    </div>

                    <!-- Contenu -->
                    <div class="blog-content">
                        {!! $article->content !!}
                    </div>
                </div>



                <!-- Section des commentaires -->

                <div class="comments-section">
                    <h3 class="comment-title">Commentaires pour cet article</h3>

                    @if ($article->comments->isEmpty())
                        <p  class="comment-item">Aucun commentaire n'a encore été ajouté pour cet article.</p>
                    @else
                        <div class="row">
                            @foreach ($article->comments as $comment)
                                <div class="col-md-6 mb-4">
                                    <div class="comment-item p-3">
                                        <h4 class="comment-author">
                                            {{ $comment->name }}
                                            ({{ $comment->email }}, +{{ $comment->country_code }}{{ $comment->phone }})
                                        </h4>
                                        <p class="comment-content">
                                            {{ $comment->content }}
                                        </p>
                                        <p class="comment-date">
                                            Posté le {{ $comment->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Barre latérale -->
            <div class="col-md-4">
                <div class="sidebar">

                    <!-- Section About -->
                    <div class="about-section mb-5">
                        <div class="section-titre">À PROPOS</div>
                        <div class="about-image">
                            <img src="{{ asset('assets/images/team/image1.png') }}" alt="About" class="img-fluid">
                        </div>
                        <p class="about-text"> Découvrez nos plats traditionnels, nos boissons naturelles et notre passion pour partager un bout de culture ivoirienne avec vous. Rejoignez-nous pour un voyage culinaire unique et savoureux !
                        </p>
                        <div class="contenu-btn">
                            <a class="read-more" href="{{ route('home') }}#apropos">Savoir plus</a>
                        </div>
                    </div>

                    <!-- Section Connect & Follow -->
                    <div class="connect-section mb-5">
                        <div class="section-titre">CONNECTEZ-VOUS ET SUIVEZ-VOUS</div>
                        <div class="social-icons-sidebar">
                            <a href="#"><i class="bi bi-facebook"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-pinterest"></i></a>
                            <a href="#"><i class="bi bi-vimeo"></i></a>
                            <a href="#"><i class="bi bi-tiktok"></i></a>
                            <a href="#"><i class="bi bi-snapchat"></i></a>
                        </div>
                    </div>

                    <!-- Section Newsletter -->
                    <div class="newsletter-section mb-5">
                        <div class="section-titre">NEWSLETTER</div>
                        <p>Entrez votre adresse email ci-dessous pour vous abonner à ma newsletter</p>
                        <form class="newsletter-form">
                            <input type="email" class="form-control form-custom-newsletter" placeholder="Votre adresse email...">
                            <div class="contenu-btn">
                                <button type="submit" class="btn subscribe-btn">S'ABONNER</button>
                            </div>
                        </form>
                    </div>

                    <!-- Derniers Blogs -->
                    <div class="latest-posts mb-5">
                        <div class="section-titre">Derniers Blogs</div>
                        @foreach ($latestBlogs as $latestBlog)
                            <a href="{{ route('admin.articles.show', $latestBlog->id) }}">
                                <div class="post">
                                    <!-- Image dynamique -->
                                    <img src="{{ url('storage/' . $latestBlog->image) }}" alt="{{ $latestBlog->title }}">
                                    <div class="post-info">
                                        <!-- Titre dynamique -->
                                        <h4>{{ $latestBlog->title }}</h4>
                                        <!-- Date formatée -->
                                        <p>{{ $latestBlog->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                </div> <!-- Fermeture correcte de la sidebar -->
            </div>
        </div>
    </div>
@endsection

@push('scriptsPhone')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="{{ asset('assets/js/global.js') }}"></script>
@endpush

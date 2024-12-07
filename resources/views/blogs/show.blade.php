@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/accueil.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/blogId.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/footer.css') }}">
@endpush

@section('content')
    <div class="container mt-5">
        <div class="row">
            <!-- Section principale (texte principal) -->
            <div class="col-md-8">
                <div class="content-section">
                    <!-- Catégorie du blog -->
                    <div class="blog-category">
                        {{ $blog->category->name ?? 'Non catégorisé' }}
                    </div>

                    <!-- Titre du blog -->
                    <h1 class="blog-title">
                        {{ $blog->title }}
                    </h1>

                    <!-- Métadonnées -->
                    <div class="blog-meta">
                        Publié le {{ $blog->created_at->format('d M Y') }}
                    </div>

                    <!-- Image principale -->
                    <div class="blog-image">
                        @if ($blog->image)
                            <img src="{{ url('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="img-fluid">
                        @else
                            <img src="{{ asset('assets/images/default-blog.jpg') }}" alt="Image par défaut" class="img-fluid">
                        @endif
                    </div>

                    <!-- Contenu -->
                    <div class="blog-content">
                        {!! $blog->content !!}
                    </div>
                </div>

                <!-- Section des commentaires -->
                <div class="comment-section">
                    <h3 class="comment-title">Laisser un commentaire</h3>
                    <form class="comment-form">
                        <div class="form-row">
                            <input type="text" placeholder="Nom" class="form-control form-custom" required>
                            <input type="email" placeholder="Email" class="form-control form-custom" required>
                            <input type="text" placeholder="Site Web" class="form-control form-custom">
                        </div>
                        <div class="form-row">
                            <textarea placeholder="Entrez votre commentaire ici..." class="form-textarea form-custom" required></textarea>
                        </div>
                        <div class="form-row-check checkbox-row">
                            <input type="checkbox" id="save-info">
                            <label for="save-info">
                                Enregistrer mon nom, email et site web dans ce navigateur pour mon prochain commentaire.
                            </label>
                        </div>
                        <div class="form-row">
                            <button type="submit" class="submit-btn">Publier le commentaire</button>
                        </div>
                    </form>
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
                            <a href="{{ route('blogs.show', $latestBlog->id) }}">
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

@extends('layouts.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/blogId.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/idTel.css') }}">
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.3.0/dist/select2-bootstrap4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
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
                        Publié le {{ $blog->created_at->format('d M Y') }} <i class="bi bi-chat">{{ $commentsCount }} {{ Str::plural('commentaire', $commentsCount) }}</i>
                        <span id="like-count-{{ $blog->id }}">
                            <i class="bi bi-heart-fill"></i>
                            {{ $blog->likes->count() }} {{ Str::plural('Like', $blog->likes->count()) }}
                        </span>
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

                    <form action="{{ route('blogs.storeComment', $blog->id) }}" method="POST" class="comment-form">
                        @csrf
                        <div class="form-row">
                            <input type="text" name="name" placeholder="Nom" class="form-control form-custom">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            <input type="email" name="email" placeholder="Email" class="form-control form-custom">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            <input type="text" name="website" placeholder="Site Web" class="form-control form-custom">
                            @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-row mt-3 mb-3">
                            <input type="tel" name="phone" id="phone" placeholder="Téléphone" class="form-control">
                            <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code') }}">
                            {{-- <input type="hidden" id="country_code{{ $blog->id }}" name="country_code" value="{{ $blog->country_code }}"> --}}
                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-row">
                            <textarea name="content" placeholder="Entrez votre commentaire ici..." class="form-textarea form-custom" ></textarea>
                            @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-row-check checkbox-row">
                            <input type="hidden" name="save_info" value="0"> <!-- Champ caché -->
                            <input type="checkbox" id="save-info" name="save_info" value="1">
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

@push('scriptsPhone')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="{{ asset('assets/js/global.js') }}"></script>
    <script src="{{ asset('assets/js/save_content.js') }}"></script>
@endpush

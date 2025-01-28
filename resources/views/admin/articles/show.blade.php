@extends('layouts.masterAdmin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/blogAdminId.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/idTel.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
@endpush

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('blog.show_blog_id') }}</h1>
            <p>{{ __('blog.show_welcome_message') }}</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container my-5">
        <div class="row">

            @php
                $locale = app()->getLocale(); // Langue actuelle
                $title = $locale === 'fr' ? $article->title_fr : $article->title_en;
                $content = $locale === 'fr' ? $article->content_fr : $article->content_en;
            @endphp
            <!-- Section principale (texte principal) -->
            <div class="col-md-8">
                <div class="content-section">
                    <!-- Catégorie du blog -->
                    <div class="blog-category">
                        {{ $article->category->name ?? 'Non catégorisé' }}
                    </div>

                    <!-- Titre du blog -->
                    <h1 class="blog-title">
                        {{ $title }}
                    </h1>

                    <!-- Métadonnées -->
                    <div class="blog-meta">
                        {{ __('blog.published_on') }} {{ $article->created_at->format('d M Y') }}  <i class="bi bi-chat"></i> {{ $commentsCount }} {{ trans_choice('blog.comments', $commentsCount) }}
                            <span id="like-count-{{ $article->id }}">
                                <i class="bi bi-heart-fill"></i>
                                {{ $article->likes->count() }} {{ trans_choice('blog.likes', $article->likes->count()) }}
                            </span>
                    </div>

                    <!-- Image principale -->
                    <div class="blog-image">
                        @if ($article->image)
                            <img src="{{ url('storage/' . $article->image) }}" alt="{{ __('blog.alt_image', ['title' => $title]) }}" class="img-fluid">
                        @else
                            <img src="{{ asset('assets/images/default-blog.jpg') }}" alt="Image par défaut" class="img-fluid">
                        @endif
                    </div>

                    <!-- Contenu -->
                    <div class="blog-content">
                        {!! $content !!}
                    </div>
                </div>



                <!-- Section des commentaires -->

                <div class="comments-section">
                    <h3 class="comment-title">{{ __('blog.comment_section_title') }}</h3>

                    @if ($article->comments->isEmpty())
                        <p  class="comment-item">{{ __('blog.no_comments') }}</p>
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
                                            {{ __('blog.posted_on') }} {{ $comment->created_at->format('d/m/Y à H:i') }}
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
                        <div class="section-titre">{{ __('blog.about_title') }}</div>
                        <div class="about-image">
                            <img src="{{ asset('assets/images/team/image1.png') }}" alt="{{ __('blog.about_title') }}" class="img-fluid">
                        </div>
                        <p class="about-text">{{ __('blog.about_text') }}</p>
                        <div class="contenu-btn">
                            <a class="read-more" href="{{ route('home') }}#apropos">{{ __('blog.about_button') }}</a>
                        </div>
                    </div>

                    <!-- Section Connect & Follow -->
                    <div class="connect-section mb-5">
                        <div class="section-titre">{{ __('blog.connect_follow_title') }}</div>
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
                        <div class="section-titre">{{ __('blog.newsletter_title') }}</div>
                        <p>{{ __('blog.newsletter_text') }}</p>
                        <form class="newsletter-form">
                            <input
                                type="email"
                                class="form-control form-custom-newsletter"
                                placeholder="{{ __('blog.newsletter_placeholder') }}">
                            <div class="contenu-btn">
                                <button type="submit" class="btn subscribe-btn">{{ __('blog.newsletter_button') }}</button>
                            </div>
                        </form>
                    </div>


                    <!-- Derniers Blogs -->
                    <div class="latest-posts mb-5">
                        <div class="section-titre">{{ __('blog.latest_blogs_title') }}</div>
                        @foreach ($latestBlogs as $latestBlog)
                            <a href="{{ route('admin.articles.show', $latestBlog->id) }}">
                                <div class="post">
                                    <!-- Image dynamique -->
                                    <img src="{{ url('storage/' . $latestBlog->image) }}" alt="{{ $latestBlog->title }}">
                                    <div class="post-info">
                                        <!-- Titre dynamique -->
                                        @php
                                            $title = app()->getLocale() === 'fr' ? $latestBlog->title_fr : $latestBlog->title_en;
                                        @endphp
                                        <h4>{{ $title }}</h4>
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

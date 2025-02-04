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

        @php
            $locale = app()->getLocale(); // Langue actuelle
            $title = $locale === 'fr' ? $blog->title_fr : $blog->title_en;
            $content = $locale === 'fr' ? $blog->content_fr : $blog->content_en;

                // Définir le format de la date en fonction de la langue
                 $dateFormat = $locale === 'fr' ? 'd F Y' : 'M d, Y';
        @endphp

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
                        {{ $title }}
                    </h1>

                    <!-- Métadonnées -->
                    <div class="blog-meta">
                        {{ __('blog.published_on') }} {{ $blog->created_at->locale($locale)->translatedFormat($dateFormat) }} <i class="bi bi-chat">{{ $commentsCount }} {{ trans_choice('blog.comments', $commentsCount) }}</i>
                        <span id="like-count-{{ $blog->id }}">
                            <i class="bi bi-heart-fill"></i>
                            {{ $blog->likes->count() }} {{ trans_choice('blog.likes', $blog->likes->count()) }}
                        </span>
                    </div>

                    <!-- Image principale -->
                    <div class="blog-image">
                        @if ($blog->image)
                        <img src="{{ url('storage/' . $blog->image) }}" alt="{{ __('blog.alt_image', ['title' => $title]) }}" class="img-fluid">
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
                            <input type="text" name="name" placeholder="{{ __('blog.name') }}" class="form-control form-custom">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror

                            <input type="email" name="email" placeholder="{{ __('blog.email') }}" class="form-control form-custom">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror

                            <input type="text" name="website" placeholder="{{ __('blog.website') }}" class="form-control form-custom">
                            @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-row mt-3 mb-3">
                            <input type="tel" name="phone" id="phone" placeholder="{{ __('blog.phone_placeholder') }}" class="form-control">
                            <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code') }}">
                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-row">
                            <textarea name="content" placeholder="{{ __('blog.content') }}" class="form-textarea form-custom"></textarea>
                            @error('content') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-row-check checkbox-row">
                            <input type="hidden" name="save_info" value="0">
                            <input type="checkbox" id="save-info" name="save_info" value="1">
                            <label for="save-info">
                                {{ __('blog.save_info') }}
                            </label>
                        </div>

                        <div class="form-row">
                            <button type="submit" class="submit-btn">{{ __('blog.submit') }}</button>
                        </div>
                    </form>

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
                                        <p>{{ $latestBlog->created_at->locale($locale)->translatedFormat($dateFormat) }}</p>
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

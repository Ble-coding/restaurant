@extends('layouts.master')


@push('styles')
{{--
<link rel="stylesheet" href="{{ asset('assets/css/service.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/team.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/apropos.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}"> --}}

@endpush


@section('headerContent')
    <div class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row">
                <!-- Text Section -->
                <div class="col-md-6 section-text">
                    <h6 class="subtitle">{{ __('home.welcome_subtitle') }}</h6>
                    <h1 class="title">{{ __('home.welcome_title') }}</h1>
                    <p class="description">
                        {{ __('home.welcome_description') }}
                    </p>
                    <a href="#apropos" class="btn btn-orange"> {{ __('home.explore_more') }}</a>
                </div>
            </div>
        </div>
        <div class="overlay"></div>
    </div>
@endsection

@section('content')

    <section id="services">
        <div class="container my-5">
            <div class="section-title">
                <h2>  {{ __('home.services_title') }}</h2>
                <p>{{ __('home.services_description') }} </p>
            </div>
            <div class="row">
                <!-- Premier service -->
                <div class="col-md-4 service-globe">
                    <div class="image_zooming">
                        <a href="#">
                            <div class="service-item">
                                <img src="./assets/images/services/service_traiteur.png"  alt="{{ __('home.catering_services') }}">
                            </div>
                            <h5>{{ __('home.catering_services') }}</h5>
                        </a>
                    </div>
                    <p>{{ __('home.catering_description') }}</p>
                </div>

                <!-- Deuxième service -->
                <div class="col-md-4 service-globe">
                    <div class="image_zooming">
                        <a href="#">
                            <div class="service-item">
                                <img src="./assets/images/services/service_jus.png" alt="{{ __('home.natural_drinks') }}">
                            </div>
                            <h5>{{ __('home.natural_drinks') }}</h5>
                        </a>
                    </div>
                    <p>{{ __('home.drinks_description') }}</p>
                </div>

                <!-- Troisième service -->
                <div class="col-md-4 service-globe">
                    <div class="image_zooming">
                        <a href="#">
                            <div class="service-item">
                                <img src="./assets/images/services/livreur.png" alt="{{ __('home.delivery_service') }}">
                            </div>
                            <h5>{{ __('home.delivery_service') }}</h5>
                        </a>
                    </div>
                    <p>{{ __('home.delivery_description') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="menu">
        
        
        <div class="slider-container">
            <div class="section-title">
                <h2>{{ __('home.available_menus') }}</h2>
                <p>{{ __('home.menus_description') }}</p>
            </div>
            <div class="row">
                <!-- Filtre par Catégorie -->
                <div class="col-md-6">
                    <div class="category-filters">
                        <div class="section-title">
                            <h6>{{ __('home.filter_by_category') }}</h6>
                        </div>
                        
                        @foreach ($categories as $category)
                            <label>
                                <input type="checkbox" class="category-filter form-custom-user" value="{{ $category->id }}">
                                {{ $category->getTranslation('name', app()->getLocale()) }}
                            </label>
                        @endforeach
                    </div>
                </div>
            
                <!-- Filtre par Statut -->
                <div class="col-md-6">
                    <div class="status-filters">
                        <div class="section-title">
                            <h6>{{ __('home.filter_by_status') }}</h6>
                        </div>
            
                        @foreach ($statuses as $key => $label)
                            <label>
                                <input type="checkbox" class="status-filter form-custom-user" value="{{ $key }}">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="swiper menu-swiper">
                <div class="swiper-wrapper" id="product-list">
                    @foreach ($products as $product)
                        <div class="swiper-slide">
                            <div class="content">
                                <h3>{{ $product->name }}</h3>
                                <p>
                                    {{ Str::limit($product->description, 150, '...') }}
                                </p>
                                <a class="{{ Route::currentRouteName() === 'menus.index'}}" 
                                   href="{{ route('menus.index') }}">
                                    Voir le Menu Complet
                                </a>
                            </div>
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 class="img-fluid"
                                 alt="{{ $product->name }}">
                        </div>
                    @endforeach
                </div>
                <!-- Navigation buttons -->
                <div class="swiper-button-prev menu-prev"></div>
                <div class="swiper-button-next menu-next"></div>
            </div>
            
        </div>
    </section>

    <!-- Section avec image de fond -->
    <section class="video-section">
        <div class="content">
            <h2>{{ __('home.restaurant_discovery') }}</h2>
            <p>
                {{ __('home.restaurant_description') }}
            </p>
            <!-- Image bouton pour jouer la vidéo -->
            <img class="play-button" width="90" height="90" src="{{ asset('/assets/images/header/play-btn.png') }}" alt="Play Video" onclick="openModal()">
        </div>

        <!-- Modale pour la vidéo -->
        <div id="videoModal" class="modal">
            <div class="modal-content">
                <!-- Intégration de la vidéo YouTube -->
                <iframe width="695" height="391" src="https://www.youtube.com/embed/4PYII8RFCHY" title="Dessert au chocolat 🍫 avec seulement 1 oeuf ! Recette facile et rapide" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
        </div>
    </section>


    {{-- <section id="boisson" class="boisson-section">
        <div class="slider-container">
            <div class="section-title">
                <h2>{{ __('home.drinks_fruits') }}</h2>
                <p>{{ __('home.drinks_fruits_description') }}</p>
            </div>
            <div class="swiper boisson-slider">
                <div class="swiper-wrapper">
                    @foreach ($productsMenusBoissons as $product)
                        <div class="swiper-slide">
                            <div class="content">
                                <h3>{{ $product->name }}</h3>
                                <p> --}}
                                    {{-- {{ $product->description }} --}}
                                    {{-- {{ Str::limit($product->description, 150, '...') }}
                                </p>

                                <a class="{{ Route::currentRouteName() === 'menus.index' && request('search') === 'boisson' ? 'active' : '' }}" href="{{ route('menus.index') }}?search=boisson">Voir les Boissons</a>
                            </div>
                            <img src="{{ asset('storage/' . $product->image) }}"
                            class="img-fluid" --}}
                            {{-- width="555" height="400"  --}}
                            {{-- alt="{{ $product->name }}">
                        </div>
                    @endforeach
                </div> --}}
                <!-- Navigation buttons -->
                {{-- <div class="swiper-button-prev boisson-prev"></div>
                <div class="swiper-button-next boisson-next"></div>
            </div>
        </div> --}}
    {{-- </section> --}}

    <section id="team">
        <div class="team-section">
            <div class="container">
                <div class="section-title">
                    <h2>{{ __('home.team_title') }}</h2>
                    <p>{{ __('home.team_description') }}</p>
                </div>
                <div class="row p-4">
 
                    <div class="col-md-4 col-sm-12">
                        <div class="team-member">
                            <div class="team-card">
                                <img src="{{ asset('/assets/images/team/image2.png') }}" alt="Chef 1" class="img-fluid rounded">
                                <div class="overlay">
                                    <a href="#"><i class="bi bi-facebook"></i></a>
                                    <a href="#"><i class="bi bi-twitter"></i></a>
                                    <a href="#"><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="team-info">
                                <h3 class="team-name">{{ __('home.team_member_1') }}</h3>
                                <p class="team-role"> {{ __('home.team_role_1') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="team-member">
                            <div class="team-card">
                                <img src="/assets/images/team/image3.png"  alt="Chef 2" class="img-fluid rounded">
                                <!-- <div class="overlay">
                                    <a href="#"><i class="bi bi-facebook"></i></a>
                                    <a href="#"><i class="bi bi-twitter"></i></a>
                                    <a href="#"><i class="bi bi-linkedin"></i></a>
                                </div>-->
                            </div>
                            <!-- <div class="team-info">
                                <h3 class="team-name">Fatoumata Traoré</h3>
                                <p class="team-role"> Responsable Logistique </p>
                            </div> -->
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="team-member">
                            <div class="team-card">
                                    <img src="{{ asset('assets/images/team/image1.png') }}" alt="Chef 3" class="img-fluid rounded">
                                <div class="overlay">
                                    <a href="#"><i class="bi bi-facebook"></i></a>
                                    <a href="#"><i class="bi bi-twitter"></i></a>
                                    <a href="#"><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                            <div class="team-info">
                                <h3 class="team-name">{{ __('home.team_member_2') }}</h3>
                                <p class="team-role"> {{ __('home.team_role_2') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-3 col-sm-6">
                        <div class="team-member">
                            <img src="./assets/images/team/image4.png" alt="Chef 4" class="img-fluid rounded">
                                <h3 class="team-name">Awa Diallo</h3>
                                <p class="team-role">Responsable Marketing</p>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </section>

    <section id="apropos">
        <div class="container py-5">
            <div class="section-title">
                <h2>{{ __('home.about_title') }}</h2>
                <p> {{ __('home.about_description') }} </p>
            </div>
            <div class="row g-4">
                <!-- Notre Histoire -->
                <div class="col-md-4">
                    <div class="about-card p-4 rounded">
                        <h3>{{ __('home.our_story') }}</h3>
                        <p>
                            {{ __('home.our_story_description') }}
                        </p>
                    </div>
                </div>
                <!-- Notre Mission -->
                <div class="col-md-4">
                    <div class="about-card p-4 rounded">
                        <h3> {{ __('home.our_mission') }}</h3>
                        <p>
                            {{ __('home.our_mission_description') }}
                        </p>
                    </div>
                </div>
                <!-- Nos Valeurs -->
                <div class="col-md-4">
                    <div class="about-card p-4  rounded">
                        <h3>    {{ __('home.our_values') }}</h3>
                        <ul>
                            <li> {!! __('home.values.authenticity') !!} </li>
                            <li> {!! __('home.values.quality') !!} </li>
                            <li> {!! __('home.values.proximity') !!} </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="blog">
        <div class="blog-section">
            <div class="container">
                <div class="section-title">
                    <h2>{{ __('home.blog_latest_news') }}</h2>
                    <p>{{ __('home.blog_description') }}</p>
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
            <div class="contenu-btn">
                {{-- <a href="#">Voir plus de blogs</a> --}}
                <a class="view-cart nav-link {{ Route::currentRouteName() === 'blogs.index' ? 'active' : '' }}" href="{{ route('blogs.index') }}">{{ __('home.view_more_blogs') }}</a>
            </div>
        </div>
    </section>


    <section id="contact">
        <div class="container py-5">
            <div class="row g-4">
                <!-- Google Maps Error Section -->
                <!-- <div class="col-md-6">
                    <div class="google-map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2484.9181180821224!2d-0.02857112338169319!3d51.47801727180659!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487602f481fcd9c5%3A0x56c4a79b926f7bf5!2sDeptford%20High%20St%2C%20London%2C%20Royaume-Uni!5e0!3m2!1sfr!2sci!4v1732286913457!5m2!1sfr!2sci" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>   -->
                <div class="col-md-6">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2484.9181180821224!2d-0.02857112338169319!3d51.47801727180659!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487602f481fcd9c5%3A0x56c4a79b926f7bf5!2sDeptford%20High%20St%2C%20London%2C%20Royaume-Uni!5e0!3m2!1sfr!2sci!4v1732286913457!5m2!1sfr!2sci" width="100" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                <!-- Contact Form Section -->
                <div class="col-md-6 bg-white p-4">
                    <form class="contact-form">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-custom" placeholder="{{ __('home.contact_form.name_placeholder') }}" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control form-control-custom" placeholder="{{ __('home.contact_form.email_placeholder') }}" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control form-control-custom" rows="4" placeholder="{{ __('home.contact_form.message_placeholder') }}" required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="w-auto px-5 btn btn-send">
                                {{ __('home.contact_form.send_message') }}
                                <img  class="btn-arrow" src="./assets/images/apropos/right_arrow_test.png" width="40" height="40" alt="arrow_right" />
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection



@push('scripts')
<script src="{{ asset('assets/js/like.js') }}"></script>
<script src="{{ asset('assets/js/modalVideo.js') }}"></script>
<script src="{{ asset('assets/js/accueilJs.js') }}"></script>
<script src="{{ asset('assets/swiper/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/swiper/js/swiper-btn.js') }}"></script>
{{-- <script src="{{ asset('assets/js/filter.js') }}"></script> --}}
<script>
$(document).ready(function () {
    $('.category-filter, .status-filter').change(function () {
        let selectedCategories = [];
        let selectedStatuses = [];

        // Récupérer les catégories cochées
        $('.category-filter:checked').each(function () {
            selectedCategories.push($(this).val());
        });

        // Récupérer les statuts cochés
        $('.status-filter:checked').each(function () {
            selectedStatuses.push($(this).val());
        });

        $.ajax({
            url: '{{ route("home") }}',
            method: 'GET',
            data: { categories: selectedCategories, statuses: selectedStatuses },
            success: function (response) {
                $('#product-list').html($(response).find('#product-list').html());
            }
        });
    });
});

</script>
@endpush
 
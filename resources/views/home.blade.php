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
                    <h6 class="subtitle">Bienvenue chez</h6>
                    <h1 class="title">Côte d'Ivoire Drinks & Foods</h1>
                    <p class="description">
                        Votre passeport culinaire pour la Côte d’Ivoire, livré directement chez vous !
                    </p>
                    <a href="#apropos" class="btn btn-orange">Explorez plus</a>
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
                <h2>Services</h2>
                <p>Faites de vos événements un succès avec nos plats ivoiriens authentiques </p>
            </div>
            <div class="row">
                <!-- Premier service -->
                <div class="col-md-4 service-globe">
                    <div class="image_zooming">
                        <a href="#">
                            <div class="service-item">
                                <img src="./assets/images/services/service_traiteur.png"  alt="Services traiteur">
                            </div>
                            <h5>Services traiteur</h5>
                        </a>
                    </div>
                    <p>Découvrez une sélection de plats ivoiriens et internationaux soigneusement préparés pour ravir les papilles de vos invités. Des entrées aux desserts, chaque assiette reflète notre passion pour la cuisine.</p>
                </div>

                <!-- Deuxième service -->
                <div class="col-md-4 service-globe">
                    <div class="image_zooming">
                        <a href="#">
                            <div class="service-item">
                                <img src="./assets/images/services/service_jus.png" alt="Boissons naturelles">
                            </div>
                            <h5>Boissons naturelles</h5>
                        </a>
                    </div>
                    <p>Offrez à vos convives des boissons artisanales, préparées à partir d’ingrédients frais et naturels. Nos jus exotiques et cocktails sans alcool sont parfaits pour accompagner vos repas.</p>
                </div>

                <!-- Troisième service -->
                <div class="col-md-4 service-globe">
                    <div class="image_zooming">
                        <a href="#">
                            <div class="service-item">
                                <img src="./assets/images/services/livreur.png" alt="Livraison soignée">
                            </div>
                            <h5>Livraison soignée</h5>
                        </a>
                    </div>
                    <p>Bénéficiez d’un service de livraison rapide et ponctuel. Nos plats sont présentés de manière élégante pour impressionner vos invités dès leur arrivée sur votre table.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="menu">
        <div class="slider-container">
            <div class="section-title">
                <h2>Nos Plats Disponibles</h2>
                <p>Découvrez nos spécialités locales et savourez des repas faits avec passion et authenticité.</p>
            </div>
            <div class="swiper">
                <div class="swiper-wrapper">
                    @foreach ($products as $product)
                        <div class="swiper-slide">
                            <div class="content">
                                <h3>{{ $product->name }}</h3>
                                <p>{{ $product->description }}</p>
                                <a class="{{ Route::currentRouteName() === 'menus.index' ? 'active' : '' }}" href="{{ route('menus.index') }}">Voir le Menu Complet</a>
                            </div>
                            <img src="{{ asset('storage/' . $product->image) }}" width="555" height="400" alt="{{ $product->name }}">
                        </div>
                    @endforeach
                </div>
                <!-- Navigation buttons -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>

    <!-- Section avec image de fond -->
    <section class="video-section">
        <div class="content">
            <h2>Découvrez Notre Restaurant et Nos Délices Savoureux</h2>
            <p>
                Plongez dans l'univers de notre cuisine avec cette vidéo exclusive. Regardez comment nos plats sont préparés avec passion et expertise.
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

    <section id="team">
        <div class="team-section">
            <div class="container">
                <div class="section-title">
                    <h2>Rencontrez Notre Équipe Dévouée</h2>
                    <p> Une équipe passionnée par la cuisine ivoirienne et les produits naturels, dédiée à vous offrir une expérience culinaire exceptionnelle.</p>
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
                                <h3 class="team-name">Yobo Michael Enzo</h3>
                                <p class="team-role"> MANAGER GENERAL</p>
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
                                <h3 class="team-name">Kacou Claude-Annette Epse Yobo</h3>
                                <p class="team-role">RESPONSABLE COMMUNICATION ET COMMERCIALE</p>
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
                <h2>À propos</h2>
                <p> Nous sommes passionnés par le partage de la richesse culinaire ivoirienne avec la diaspora et tous les amateurs de cuisine africaine </p>
            </div>
            <div class="row g-4">
                <!-- Notre Histoire -->
                <div class="col-md-4">
                    <div class="about-card p-4 rounded">
                        <h3>Notre Histoire</h3>
                        <p>
                            Côte d’Ivoire Drinks & Foods est né de notre passion pour la cuisine ivoirienne. Basés à Londres,
                            nous avons décidé de partager cette richesse culinaire avec vous, en proposant des plats et boissons
                            qui rappellent la chaleur et l’hospitalité de la Côte d’Ivoire.
                        </p>
                    </div>
                </div>
                <!-- Notre Mission -->
                <div class="col-md-4">
                    <div class="about-card p-4 rounded">
                        <h3>Notre Mission</h3>
                        <p>
                            Nous nous engageons à offrir une expérience culinaire authentique, basée sur des recettes
                            traditionnelles et des ingrédients de qualité. Notre objectif est de rapprocher la diaspora
                            ivoirienne et les amateurs de cuisine africaine de la culture culinaire de notre pays.
                        </p>
                    </div>
                </div>
                <!-- Nos Valeurs -->
                <div class="col-md-4">
                    <div class="about-card p-4  rounded">
                        <h3>Nos Valeurs</h3>
                        <ul>
                            <li><strong>Authenticité</strong> : Chaque plat est préparé selon les recettes traditionnelles.</li>
                            <li><strong>Qualité</strong> : Nous utilisons des ingrédients frais et sélectionnés avec soin.</li>
                            <li><strong>Proximité</strong> : Nous livrons vos plats directement à domicile, pour votre confort.</li>
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
            <div class="contenu-btn">
                {{-- <a href="#">Voir plus de blogs</a> --}}
                <a class="view-cart nav-link {{ Route::currentRouteName() === 'blogs.index' ? 'active' : '' }}" href="{{ route('blogs.index') }}">Voir plus de blogs</a>
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
                            <input type="text" class="form-control form-control-custom" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control form-control-custom" placeholder="Enter email address" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control form-control-custom" rows="4" placeholder="Message" required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="w-auto px-5 btn btn-send">
                                ENVOYER UN MESSAGE
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
@endpush

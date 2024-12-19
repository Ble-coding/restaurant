@extends('layouts.master404')


@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/accueil.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/service.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/menu.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/team.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/apropos.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/footer.css') }}">
@endpush


@section('headerContent')
    <div class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row">
                <!-- 404 Section -->
                <div class="col-md-6 section-text">
                    <h6 class="subtitle">Erreur 404</h6>
                    <h1 class="title">Page non trouvée</h1>
                    <p class="description">
                        Oups ! La page que vous recherchez est introuvable. Retournez à la page d'accueil pour continuer votre navigation.
                    </p>
                    <a href="{{ route('home') }}" class="btn btn-orange mt-4">Retourner à l'accueil</a>
                </div>
            </div>
        </div>
        <div class="overlay"></div>
    </div>
@endsection

@section('content')



@endsection



@push('scripts')
<script src="{{ asset('assets/js/modalVideo.js') }}"></script>
<script src="{{ asset('assets/js/accueilJs.js') }}"></script>
<script src="{{ asset('assets/swiper/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/swiper/js/swiper-btn.js') }}"></script>
@endpush

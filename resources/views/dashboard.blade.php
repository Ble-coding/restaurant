{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}


{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4 text-dark">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body text-secondary">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}


@extends('layouts.masterAdmin')


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
                <!-- Text Section -->
                <div class="col-md-6 section-text">
                    <h6 class="subtitle">Bienvenue chez</h6>
                    <h1 class="title">Côte d'Ivoire Drinks & Foods</h1>
                    <p class="description">
                        Votre passeport culinaire pour la Côte d’Ivoire, livré directement chez vous !
                    </p>
                    <a href="{{ route('home') }}#apropos" class="btn btn-orange">Explorez le site</a>
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

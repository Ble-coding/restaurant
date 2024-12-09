<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link href="{{ asset('assets/swiper/css/swiper-bundle.min.css') }}" rel="stylesheet">
    <title>{{ config('app.name') }} - @yield('title', 'Bienvenue')</title>
    <link rel="icon" href="{{ asset('assets/images/logo_png.ico') }}" type="image/x-icon">
    <link href="{{ asset('assets/bootstrap/css/bootstrap-icons.css') }}" rel="stylesheet">
    @include('layouts.head')
    @stack('styles')
</head>
<body>
    <section id="accueil">
        @include('layouts.header')
    </section>

    @yield('content')

    @include('layouts.footer')



    @stack('scriptstoggle')
    @include('layouts.footer-scripts')
    @stack('scripts')
    @stack('scriptsPhone')
    @stack('scriptsCart')
    @stack('scriptsCheckout')

</body>
</html>

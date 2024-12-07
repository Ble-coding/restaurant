<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{ asset('assets/images/header/hamburger_menu_button.png') }}" width="40" height="40" alt="Menu" />
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                @if (Route::has('login'))
                    @auth
                        <!-- Lien pour les utilisateurs authentifiés -->
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                Dashboard
                            </a>
                        </li>
                    @else
                        <!-- Lien pour la connexion -->
                        <li class="nav-item">
                            <a class="nav-link {{ Route::currentRouteName() === 'login' ? 'active' : '' }}" href="{{ route('login') }}">
                                Login
                            </a>
                        </li>

                        <!-- Lien pour l'inscription, si disponible -->
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link {{ Route::currentRouteName() === 'register' ? 'active' : '' }}" href="{{ route('register') }}">
                                    Register
                                </a>
                            </li>
                        @endif
                    @endauth
                @endif
            </ul>
        </div>

    </div>
</nav>

<!-- Hero Section -->
<div class="hero-img">
    <a class="navbar-brand mt-4" href="#">
        <img src="{{ asset('assets/images/header/logo_png.png') }}" width="100" height="100" alt="Logo" />
    </a>
</div>


<div class="header-section">
    @yield('headerContent')
</div>

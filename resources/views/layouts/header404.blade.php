<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{ asset('assets/images/header/hamburger_menu_button.png') }}" width="40" height="40" alt="Menu" />
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                {{-- <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'home' ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
                </li> --}}
              <!-- Vérification si vous êtes sur la page des menus -->
              {{-- <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() === 'menus.index' ? 'active' : '' }}" href="{{ route('menus.index') }}">Menus</a>
            </li> --}}

                {{-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Equipes
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('home') }}#team">Team</a></li>
                        <li><a class="dropdown-item" href="{{ route('home') }}#apropos">À propos</a></li>
                        <li><a class="dropdown-item" href="{{ route('home') }}#services">Services</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'customer.orders.index' ? 'active' : '' }}" href="{{ route('customer.orders.index') }}">Commandes</a>
                </li> --}}

              <!-- Vérification si vous êtes sur la page des blogs -->
                {{-- <li class="nav-item">
                    <a class="nav-link {{ in_array(Route::currentRouteName(), ['blogs.index', 'blogs.show']) ? 'active' : '' }}" href="{{ route('blogs.index') }}">Blogs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#contact">Contact</a>
                </li> --}}
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

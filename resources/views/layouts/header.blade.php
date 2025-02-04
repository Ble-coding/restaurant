<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{ asset('assets/images/header/hamburger_menu_button.png') }}" width="40" height="40" alt="Menu" />
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'home' ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
                </li>
              <!-- Vérification si vous êtes sur la page des menus -->
              <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() === 'menus.index' ? 'active' : '' }}" href="{{ route('menus.index') }}">Menus</a>
            </li>

                <li class="nav-item dropdown">
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
                </li>

              <!-- Vérification si vous êtes sur la page des blogs -->
                <li class="nav-item">
                    <a class="nav-link {{ in_array(Route::currentRouteName(), ['blogs.index', 'blogs.show']) ? 'active' : '' }}" href="{{ route('blogs.index') }}">Blogs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#contact">Contact</a>
                </li>

                <li class="nav-item dropdown menu-item-has-children">
                    <a class="nav-link fw-medium dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        @php
                            $locale = app()->getLocale();
                            $languageLabel = $locale === 'fr' ? 'Français' : 'English';
                            $flagIcon = $locale === 'fr' ? 'fr.png' : 'us.png';
                        @endphp
                        <img src="{{ asset('assets/images/header/' . $flagIcon) }}" class="me-1" alt="{{ $languageLabel }}">
                        {{ $languageLabel }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('set.locale', ['lang' => 'fr']) }}">
                                <img src="{{ asset('assets/images/header/fr.png') }}" class="me-1" alt="Français">
                                {{ $locale === 'fr' ? __('Français') : __('French') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               href="{{ route('set.locale', ['lang' => 'en']) }}">
                                <img src="{{ asset('assets/images/header/us.png') }}" class="me-1" alt="English">
                                {{ $locale === 'fr' ? __('Anglais') : __('English') }}
                            </a>
                        </li>
                    </ul>
                </li>


                @if(Auth::guard('customer')->check())
                <li class="nav-item" style="position: relative;">
                    <a class="nav-link" href="{{ route('cart.view') }}">
                        <span class="badge" id="cart-badge">0</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16">
                            <path d="M8 1a2 2 0 0 0-2 2v1H3.5A1.5 1.5 0 0 0 2 5.5v9A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 12.5 4H10V3a2 2 0 0 0-2-2Zm-1 2a1 1 0 0 1 2 0v1H7V3Zm-3.5 2H12.5a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5Z"/>
                        </svg>
                    </a>
                </li>

                {{-- Si le client est connecté --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::guard('customer')->user()->last_name }} {{ Auth::guard('customer')->user()->first_name }} {{-- Affiche le nom du client connecté --}}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            {{-- Formulaire de déconnexion --}}
                            <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Déconnexion
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                {{-- Si le client n'est pas connecté --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Connexion
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('customer.login') }}">{{ __('Se connecter') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.register') }}">{{ __('S\'enregistrer') }}</a></li>
                    </ul>
                </li>
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

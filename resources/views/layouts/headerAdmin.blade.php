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
                @role('Admin')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['admin.users.index', 'admin.roles.index', 'admin.permissions.index', 'admin.coupons.index']) ? 'active' : '' }}"
                           href="#"
                           id="navbarDropdownUsers"
                           role="button"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">
                            Utilisateurs
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownUsers">
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.users.index' ? 'active' : '' }}"
                                   href="{{ route('admin.users.index') }}">
                                    User
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.roles.index' ? 'active' : '' }}"
                                   href="{{ route('admin.roles.index') }}">
                                    Role
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.permissions.index' ? 'active' : '' }}"
                                   href="{{ route('admin.permissions.index') }}">
                                    Permission
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.coupons.index' ? 'active' : '' }}"
                                   href="{{ route('admin.coupons.index') }}">
                                    Coupons
                                </a>
                            </li>
                        </ul>
                    </li>
                @endrole

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'admin.products.index' ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Menus</a>
                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'admin.commandes.index' ? 'active' : '' }}" href="{{ route('admin.commandes.index') }}">Commandes</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['admin.articles.index', 'admin.articles.create', 'admin.articles.show']) ? 'active' : '' }}"
                       href="#"
                       id="navbarDropdown"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Blogs
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item {{ Route::currentRouteName() === 'admin.articles.index' ? 'active' : '' }}"
                               href="{{ route('admin.articles.index') }}">
                                Liste
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ Route::currentRouteName() === 'admin.articles.create' ? 'active' : '' }}"
                               href="{{ route('admin.articles.create') }}">
                                Créer un article
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item {{ Route::currentRouteName() === 'admin.categories.index' ? 'active' : '' }}"
                               href="{{ route('admin.categories.index') }}">
                                Catégorie
                            </a>
                        </li>
                    </ul>
                </li>


              <!-- Vérification si vous êtes sur la page des blogs -->

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{route('admin.profile.edit')}}">{{ __('Profile') }}</a></li>
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Déconnexion
                            </a>
                        </li>

                    </ul>
                </li>

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

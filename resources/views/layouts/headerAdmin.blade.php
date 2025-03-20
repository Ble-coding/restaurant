<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <img src="{{ asset('assets/images/header/hamburger_menu_button.png') }}" width="40" height="40" alt="Menu" />
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">{{ __('header.home') }}</a>
                </li>
                @hasanyrole('super_administrator|administrator')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['admin.users.index', 'admin.roles.index', 'admin.permissions.index', 'admin.coupons.index']) ? 'active' : '' }}"
                           href="#"
                           id="navbarDropdownUsers"
                           role="button"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">
                           {{ __('header.users') }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownUsers">
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.users.index' ? 'active' : '' }}"
                                   href="{{ route('admin.users.index') }}">
                                   {{ __('header.users') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.roles.index' ? 'active' : '' }}"
                                   href="{{ route('admin.roles.index') }}">
                                   {{ __('header.roles') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.permissions.index' ? 'active' : '' }}"
                                   href="{{ route('admin.permissions.index') }}">
                                   {{ __('header.permissions') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.coupons.index' ? 'active' : '' }}"
                                   href="{{ route('admin.coupons.index') }}">
                                   {{ __('header.coupons') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.payments.index' ? 'active' : '' }}"
                                   href="{{ route('admin.payments.index') }}">
                                   {{ __('header.payments') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.gateways.index' ? 'active' : '' }}"
                                   href="{{ route('admin.gateways.index') }}">
                                   {{ __('header.gateways') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.shippings.index' ? 'active' : '' }}"
                                   href="{{ route('admin.shippings.index') }}">
                                   {{ __('header.shippings') }}
                                </a>
                            </li>
                            {{-- <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.translations.index' ? 'active' : '' }}"
                                   href="{{ route('admin.translations.index') }}">
                                    Clé API de Traduction
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ Route::currentRouteName() === 'admin.services.index' ? 'active' : '' }}"
                                   href="{{ route('admin.services.index') }}">
                                    Service Clé API Traduction
                                </a>
                            </li> --}}
                        </ul>
                    </li>
                @endhasanyrole

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'admin.products.index' ? 'active' : '' }}" href="{{ route('admin.products.index') }}">{{ __('header.menus') }}</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'admin.categories.index' ? 'active' : '' }}"
                       href="{{ route('admin.categories.index') }}">
                        {{ __('header.category') }}
                    </a>
                </li>

                {{-- @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin')) --}}
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'admin.commandes.index' ? 'active' : '' }}" href="{{ route('admin.commandes.index') }}">{{ __('header.orders') }}</a>
                </li>
                {{-- @elseif (auth()->user()->hasRole('manager') || auth()->user()->can('view-orders'))
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() === 'managers.index' ? 'active' : '' }}" href="{{ route('managers.index') }}">Commandes</a>
                    </li>--}}
                {{-- @endif --}}


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ in_array(Route::currentRouteName(), ['admin.articles.index', 'admin.articles.create', 'admin.articles.show']) ? 'active' : '' }}"
                       href="#"
                       id="navbarDropdown"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                       {{ __('header.blogs') }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item {{ Route::currentRouteName() === 'admin.articles.index' ? 'active' : '' }}"
                               href="{{ route('admin.articles.index') }}">
                               {{ __('header.list') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ Route::currentRouteName() === 'admin.articles.create' ? 'active' : '' }}"
                               href="{{ route('admin.articles.create') }}">
                               {{ __('header.create_blog') }}
                            </a>
                        </li>
                    </ul>
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



              <!-- Vérification si vous êtes sur la page des blogs -->

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{route('admin.profile.edit')}}"> {{ __('header.profile') }}</a></li>
                        <li>
                            <form id="logout-form" action="{{ route('dashboard.exit') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('header.logout') }}
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

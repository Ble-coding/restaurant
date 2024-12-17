


@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Utilisateurs</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@section('content')


    <div class="container my-5">

        <div class="search-wrapper">
            <div class="search-container">
                <form method="GET" action="{{ route('admin.users.index') }}" id="search-form">
                    <input
                        type="text"
                        id="search"
                        class="search-input"
                        name="search"
                        placeholder="Rechercher par nom..."
                        value="{{ request()->get('search') }}"
                    >
                </form>
            </div>
        </div>


         <!-- Début des items de menu -->
         <div class="row">
            @if(session('success'))
                <div class="alert alert-success" id="success-alert">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger"  id="error-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Début des items de menu -->
            <div class="row">
                @foreach ($users as $user)
                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-image">
                                <img src="{{ asset('assets/images/menu/cat-7.4684bd1d-min.png') }}" alt="{{ $user->name }}">
                            </div>
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title">
                                        {{ $user->name }}
                                    </h3>
                                    <div class="menu-item-dots"></div>
                                    <div class="menu-item-price">
                                    +{{ $user->country_code }}{{ $user->phone }}
                                    </div>
                                </div>
                                <p class="menu-item-description">
                                    {{ $user->email }}

                                    <!-- Boutons de modification et de suppression -->
                                    <a href="#" class="add_cart m-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->id }}">✏️</a>
                                    <a href="#" class="add_cart m-3" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">🗑️</a>
                                </p>
                                <span class="menu-badge">
                                    <strong>Rôles (permissions) :</strong>
                                    {!! $user->roles->map(function($role) {
                                        $permissions = $role->permissions->pluck('name')
                                            ->map(fn($p) => ucfirst($p))
                                            ->chunk(2) // Regroupe les permissions par 2
                                            ->map(fn($chunk) => $chunk->join(', ')) // Joint les permissions dans chaque groupe
                                            ->join('<br>'); // Retour à la ligne après chaque groupe
                                        return ucfirst($role->name) . " ($permissions)";
                                    })->join(', ') !!}
                                </span>

                            </div>
                        </div>

                        <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-xl"> <!-- Extra-large modal avec scrol -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Modifier l'utilisateur : {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <!-- Première ligne -->
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="name" class="form-label">Nom complet</label>
                                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" >
                                                    {{-- @error('name') <span class="text-danger">{{ $message }}</span> @enderror --}}
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" >
                                                    {{-- @error('email') <span class="text-danger">{{ $message }}</span> @enderror --}}
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="phone" class="form-label">Téléphone</label>
                                                    <input type="text" id="phone{{ $user->id }}" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" >
                                                    <input type="hidden" id="country_code{{ $user->id }}" name="country_code" value="{{ $user->country_code }}">
                                                    {{-- @error('phone') <span class="text-danger">{{ $message }}</span> @enderror --}}
                                                </div>
                                            </div>

                                            <!-- Deuxième ligne -->
                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="roles" class="form-label">Rôle(s)</label>
                                                    <select name="roles[]" id="rolesUpdate" class="form-select select-role" multiple>
                                                        @foreach($roles as $role)
                                                            <option value="{{ $role->name }}"
                                                                {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'selected' : '' }}>
                                                                {{ ucfirst($role->name) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    {{-- @error('roles') <span class="text-danger">{{ $message }}</span> @enderror --}}
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="password" class="form-label">Mot de passe</label>
                                                    <input type="password" name="password" class="form-control mb-1" placeholder="Nouveau mot de passe">
                                                    <span class="menu-badge">(laisser vide pour ne pas modifier)</span>
                                                    {{-- @error('password') <span class="text-danger">{{ $message }}</span> @enderror --}}
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmez le mot de passe">
                                                    {{-- @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror --}}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-orange">Modifier</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal pour la suppression -->
                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Supprimer l'utilisateur : {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Confirmez-vous la suppression de <strong>{{ $user->name }}</strong> ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Fin des items de menu -->
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="pagination-container">
                    {{ $users->links('vendor.pagination.custom') }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>Enregistrement</h3>
                    <hr>
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <input type="text" name="name" class="form-control form-custom-user me-2" placeholder="Nom Complet" value="{{ old('name') }}" >
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <input type="email" name="email" class="form-control form-custom-user me-2" placeholder="Adresse email" value="{{ old('email') }}" >
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <div clas="djobo-background-store">
                            <input type="tel" id="phone" name="phone" class="form-control form-custom-user me-2" placeholder="Tel" value="{{ old('phone') }}" >
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code') }}">

                        <div class="mb-3 mt-2">
                            <label for="roles" class="form-label">Rôle(s)</label>
                            <select name="roles[]" id="roles" class="form-select form-custom-user me-2" multiple data-placeholder="Choisissez les rôles">
                                {{-- <option  value=""></option> --}}
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ in_array($role->name, old('roles', [])) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control form-custom-user me-2 " >
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror

                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="form-control form-custom-user me-2" >
                        @error('password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror


                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">Soumettre</button>
                        </div>
                    </form>
                </div>
            </div>


        </div>


    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/search.js') }}"></script>
<script>

                    // Initialiser select2 avec un z-index personnalisé
                    $('.select-role').select2({
                    dropdownParent: $('#editModal{{ $user->id }}'), // Limite le dropdown au modal
                    width: '100%', // S'assure que le dropdown s'aligne bien avec l'input
                });;
</script>

@endpush

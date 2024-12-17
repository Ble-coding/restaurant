
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Roles</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">

    <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.roles.index') }}" id="search-form">
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

            @foreach ($roles as $role)
                <div class="col-md-3 col-lg-6 mb-4">
                    <div class="menu-item p-3">
                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <h3 class="menu-item-title">{{ $role->name }}</h3>
                                <div class="menu-item-dots"></div>
                                <div class="menu-item-price">
                                    <span class="menu-badge">
                                        {{ $role->guard_name }}
                                    </span>
                                    <span class="menu-badge">
                                        {!! $role->permissions->pluck('name')
                                            ->map(fn($p) => ucfirst($p)) // Capitalise les permissions
                                            ->chunk(2) // Regroupe par paires
                                            ->map(fn($chunk) => $chunk->join(', ')) // Joint les permissions dans chaque groupe
                                            ->join('<br>') // Insère un saut de ligne après chaque groupe
                                        !!}
                                    </span>

                                </div>
                            </div>
                            {{--
                           <ul class="list-group mt-3">
                                <li class="list-group-item active">Permissions :</li>
                                @foreach ($role->permissions as $permission)
                                    <li class="list-group-item">{{ $permission->name }}</li>
                                @endforeach
                            </ul> --}}
                            <p class="menu-item-description">
                                <span class="texte">{{ $role->translation }}</span>

                                <!-- Bouton pour ouvrir le modal de modification -->
                                <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $role->id }}">✏️</a>


                                <!-- Bouton pour ouvrir le modal de suppression -->
                                <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $role->id }}">🗑️</a>
                            </p>



                        </div>
                    </div>
                </div>

                <!-- Modal pour la modification -->
                <div class="modal fade" id="editModal{{ $role->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $role->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel{{ $role->id }}">Modification du rôle: {{ $role->translation }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" class="form-control form-custom-user me-2" name="name" value="{{ old('name', $role->name) }}" placeholder="Libellé" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <!-- Sélect multiple des permissions -->
                                    <div class="mb-3">
                                        <label for="permissions{{ $role->id }}" class="form-label">Permissions</label>
                                        <select name="permissions[]" id="permissions{{ $role->id }}" class="form-control" multiple>
                                            @foreach($permissions as $permission)
                                                <option value="{{ $permission->id }}" {{ $role->permissions->contains($permission->id) ? 'selected' : '' }}>
                                                    {{ $permission->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('permissions')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="cart-actions mt-4">
                                        <button type="submit" class="view-cart">Modifier</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Modal pour la suppression -->
                <div class="modal fade" id="deleteModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $role->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $role->id }}">Confirmer la suppression</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Êtes-vous sûr de vouloir supprimer le rôle <strong>{{ $role->name }}</strong> ? Cette action est irréversible.</p>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


    <div class="row">
        <div class="col-md-6">
            <div class="pagination-container">
                {{ $roles->links('vendor.pagination.custom') }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="cart-container-width">
                <h3>Role</h3>
                <hr>
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
                    <input type="text" class="form-control form-custom-user me-2" name="name" placeholder="Libellé">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror


                       <!-- Sélect multiple des permissions -->
                        <div class="mb-3">
                            <label for="permissions" class="form-label">Permissions</label>
                            <select name="permissions[]" id="permissions" class="form-control" multiple>
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                            @error('permissions')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
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
@endpush


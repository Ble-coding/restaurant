
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('role.title') }}</h1>
            <p>{{ __('role.welcome_message') }}</p>
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
                    placeholder="{{ __('role.search_placeholder') }}"
                    value="{{ request()->get('search') }}"
                >
            </form>
        </div>
    </div>




        <!-- Début des items de menu -->
        <div class="row">
            @if(session('success'))
                <div class="alert alert-success" id="success-alert">
                    {{ __('role.success_message') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger"  id="error-alert">
                    <ul>
                        @foreach($errors->all() as $error)
                             <li>{{ __('role.error_message') }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @foreach ($roles as $role)
            @php
                $locale = app()->getLocale(); // Récupère la langue actuelle (fr ou en)
                $name = $locale === 'fr' ? $role->name_fr : $role->name_en;
            @endphp
                <div class="col-md-3 col-lg-6 mb-4">
                    <div class="menu-item p-3">
                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <h3 class="menu-item-title">
                                    {{ $name }}
                                </h3>
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
                                <span class="texte">{{ $role->name }}</span>

                                <!-- Bouton pour ouvrir le modal de modification -->
                                @can('edit-roles')

                                       <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $role->id }}">✏️</a>

                                @endcan


                                <!-- Bouton pour ouvrir le modal de suppression -->
                                @can('edit-roles')

                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $role->id }}">🗑️</a>

                                @endcan
                            </p>



                        </div>
                    </div>
                </div>

                <!-- Modal pour la modification -->
                <div class="modal fade" id="editModal{{ $role->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $role->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel{{ $role->id }}">{{ __('role.edit_role', ['name' => $role->translation]) }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                                    @csrf
                                    @method('PUT')
                                    {{-- <input type="text" class="form-control form-custom-user me-2" name="name" value="{{ old('name', $role->name) }}" placeholder="{{ __('role.role_label') }}" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror --}}

                                         <!-- Champ pour le nom en français -->
                                        <input type="text" class="form-control form-custom-user me-2" name="name_fr" 
                                        value="{{ old('name_fr', $role->name_fr) }}" 
                                        placeholder="{{ __('role.role_label_fr') }}" required>
                                    @error('name_fr')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <!-- Champ pour le nom en anglais -->
                                    <input type="text" class="form-control form-custom-user me-2" name="name_en" 
                                        value="{{ old('name_en', $role->name_en) }}" 
                                        placeholder="{{ __('role.role_label_en') }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <!-- Sélect multiple des permissions -->
                                    <div class="mb-3">
                                        <label for="permissions{{ $role->id }}" class="form-label">{{ __('role.permissions') }}</label>
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
                                        <button type="submit" class="view-cart">{{ __('role.edit') }}</button>
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
                                <h5 class="modal-title" id="deleteModalLabel{{ $role->id }}">{{ __('role.confirm_delete') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>{{ __('role.delete_message', ['name' => $role->name]) }}</p>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('role.cancel') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('role.delete') }}</button>
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
        @can('create-roles')
        <div class="col-md-6">
            <div class="cart-container-width">
                <h3>Role</h3>
                <hr>
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
                    {{-- <input type="text" class="form-control form-custom-user me-2" name="name" placeholder="{{ __('role.role_label') }}">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror --}}

                      <!-- Champ pour le nom en français -->
                    <input type="text" class="form-control form-custom-user me-2" name="name_fr" placeholder="{{ __('role.role_label_fr') }}" value="{{ old('name_fr') }}" required>
                    @error('name_fr')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror

                    <!-- Champ pour le nom en anglais -->
                    <input type="text" class="form-control form-custom-user me-2" name="name_en" placeholder="{{ __('role.role_label_en') }}" value="{{ old('name_en') }}" required>
                    @error('name_en')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror



                       <!-- Sélect multiple des permissions -->
                        <div class="mb-3">
                            <label for="permissions" class="form-label">{{ __('role.permissions') }}</label>
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
                        <button type="submit" class="view-cart">{{ __('role.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
        @endcan
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/search.js') }}"></script>
@endpush


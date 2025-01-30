
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('permission.header_title') }}</h1>
            <p>{{ __('permission.dashboard_message') }}</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">

    <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.permissions.index') }}" id="search-form">
                <input
                    type="text"
                    id="search"
                    class="search-input"
                    name="search"
                    placeholder="{{ __('permission.search_placeholder') }}"
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

            @foreach ($permissions as $permission)
            @php
                $locale = app()->getLocale(); // Récupère la langue actuelle (fr ou en)
                $name = $locale === 'fr' ? $permission->name_fr : $permission->name_en;
            @endphp
                <div class="col-md-3 col-lg-6 mb-4">
                    <div class="menu-item p-3">
                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <h3 class="menu-item-title">{{ $name }}</h3>
                                <div class="menu-item-dots"></div>
                                <div class="menu-item-price"><span class="menu-badge">{{ $permission->guard_name }}</span></div>
                            </div>
                            <p class="menu-item-description">
                                <span class="texte">{{ $permission->name }}</span>


                                <!-- Bouton pour ouvrir le modal de modification -->
                                @can('edit-permissions')
                                  <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $permission->id }}">✏️</a>
                                @endcan
                                @can('delete-permissions')
                                    <!-- Bouton pour ouvrir le modal de suppression -->
                                    <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $permission->id }}">🗑️</a>
                                @endcan
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Modal pour la modification -->
                {{-- <div class="modal fade" id="editModal{{ $permission->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $permission->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel{{ $permission->id }}">Modification du rôle: {{ $permission->translation }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('admin.permissions.update', $permission->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" class="form-control form-custom-user me-2" name="name" value="{{ old('name', $permission->name) }}" placeholder="Libellé" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="cart-actions mt-4">
                                        <button type="submit" class="view-cart">Modifier</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <!-- Modal pour la modification -->
                <div class="modal fade" id="editModal{{ $permission->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $permission->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel{{ $permission->id }}">{{ __('permission.edit_permission', ['name' => $permission->translation]) }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('admin.permissions.update', $permission->id) }}">
                                    @csrf
                                    @method('PUT')

                                    <!-- Sélection de l'action -->
                                    <div class="m-1">
                                        <label for="action">{{ __('permission.select_action') }}</label>
                                        <select name="action" id="action" class="form-select form-custom-user me-2" required>
                                            @php
                                                $parts = explode('-', $permission->name); // Diviser name en action et resource
                                                $currentAction = $parts[0] ?? ''; // Récupérer l'action actuelle
                                            @endphp
                                            <option value="create" {{ old('action', $currentAction) == 'create' ? 'selected' : '' }}>
                                                {{ __('permission.create') }}
                                            </option>
                                            <option value="view" {{ old('action', $currentAction) == 'view' ? 'selected' : '' }}>
                                                {{ __('permission.view') }}
                                            </option>
                                            <option value="edit" {{ old('action', $currentAction) == 'edit' ? 'selected' : '' }}>
                                                {{ __('permission.edit') }}
                                            </option>
                                            <option value="delete" {{ old('action', $currentAction) == 'delete' ? 'selected' : '' }}>
                                                {{ __('permission.delete') }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Sélection de la ressource -->
                                    {{-- <div class="m-1">
                                        <label for="resource">{{ __('permission.select_resource') }}</label>
                                        <select name="resource" id="permissions{{ $permission->id }}" class="form-select form-custom-user me-2" required>
                                            @php
                                                $currentResource = $parts[1] ?? ''; // Récupérer la ressource actuelle
                                            @endphp
                                            @foreach (App\Models\Permission::getResources() as $resource)
                                                <option value="{{ $resource }}" {{ old('resource', $currentResource) == $resource ? 'selected' : '' }}>
                                                    {{ ucfirst($resource) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> --}}

                                    <div class="m-1">
                                        <label for="resource">{{ __('permission.select_resource') }}</label>
                                        <select name="resource" id="permissions{{ $permission->id }}" class="form-select form-custom-user select2 me-2" required>
                                            @php
                                                $currentResource = $parts[1] ?? ''; // Récupérer la ressource actuelle
                                            @endphp
                                            @foreach (App\Models\Permission::getTranslatedResources() as $key => $resource)
                                                <option value="{{ $key }}" {{ old('resource', $currentResource) == $key ? 'selected' : '' }}>
                                                    {{ ucfirst($resource) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Bouton de soumission -->
                                    <div class="cart-actions mt-4">
                                        <button type="submit" class="view-cart">{{ __('permission.update') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Modal pour la suppression -->
                <div class="modal fade" id="deleteModal{{ $permission->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $permission->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $permission->id }}">{{ __('permission.delete_permission') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>{!! __('permission.delete_message', ['name' => $permission->name]) !!}</p>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('admin.permissions.destroy', $permission->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('permission.cancel') }}</button>
                                    <button type="submit" class="btn btn-danger">{{ __('permission.delete') }}</button>
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
                {{ $permissions->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-permissions')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>Permission</h3>
                    <hr>
                    {{-- <form method="POST" action="{{ route('admin.permissions.store') }}">
                        @csrf
                        <input type="text" class="form-control form-custom-user me-2" name="name" placeholder="Libellé">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">Soumettre</button>
                        </div>
                    </form> --}}
                    <form method="POST" action="{{ route('admin.permissions.store') }}">
                        @csrf
                        <div class="m-1" >
                            <label for="action">{{ __('permission.select_action') }}</label>
                            <select name="action" id="action" class="form-select form-custom-user me-2" >
                                <option value="create">Create</option>
                                <option value="view">View</option>
                                <option value="edit">Edit</option>
                                <option value="delete">Delete</option>
                            </select>
                        </div>
                        <div class="m-1">
                            <label for="resource">{{ __('permission.select_resource') }}</label>
                            <select id="permissions" name="resource" class="form-select form-custom-user me-2">
                                @foreach (App\Models\Permission::getTranslatedResources() as $key => $resource)
                                    <option value="{{ $key }}">{{ ucfirst($resource) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">{{ __('permission.submit') }}</button>
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
    <!-- Inclure le fichier JS de select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>

    $(document).ready(function () {
        // Initialisation de Select2 globalement
        $('#permissions').select2({
            theme: 'bootstrap-5',
            placeholder: "{{ __('permission.choose_permissions') }}",
            allowClear: true
        });

        $('.modal').on('shown.bs.modal', function () {
            $(this).find('select.select2').each(function () {
                let selectId = $(this).attr('id');
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $('#' + selectId).select2({
                        theme: 'bootstrap-5',
                        placeholder: "{{ __('permission.choose_permissions') }}",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#' + selectId).closest('.modal') // Corrige l'affichage dans le modal
                    });
                }
            });
        });
    });


        window.addEventListener('DOMContentLoaded', (event) => {
        let successAlert = document.getElementById('success-alert');
        if (successAlert) {
            console.log('Success alert found'); // Pour déboguer
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 5000);
        }

        let errorAlert = document.getElementById('error-alert');
        if (errorAlert) {
            console.log('Error alert found'); // Pour déboguer
            setTimeout(() => {
                errorAlert.style.display = 'none';
            }, 5000);
        }
     });
    </script>
@endpush


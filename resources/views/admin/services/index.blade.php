
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Services de Traduction</h1>
            <p>Gérez vos services de traduction ici.</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">
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

            @foreach ($services as $service)

            <div class="col-md-3 col-lg-6 mb-4">
                <div class="menu-item p-3">
                    <div class="menu-item-content">
                        <div class="menu-item-header">
                            <h3 class="menu-item-title">{{ $service->name }}</h3>
                            <div class="menu-item-dots"></div>
                            <div class="menu-item-price">
                                <span class="menu-badge api-key" title="{{ $service->identifier }}">
                                    IDENTIFIANT {{ $service->identifier }}
                                </span>

                            </div>
                        </div>
                        <p class="menu-item-description">

                            <span class="menu-badge">
                                DESCRIPTION {{ $service->description }}
                            </span>

                            @can('edit-services')
                              <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $service->id }}">✏️</a>
                            @endcan
                            @can('delete-services')
                              <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $service->id }}">🗑️</a>
                            @endcan
                        </p>
                    </div>
                </div>
            </div>


               <!-- Modal pour la modification -->
               <div class="modal fade" id="editModal{{ $service->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $service->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.services.update', $service->id) }}">
                                @csrf
                                @method('PUT')


                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" class="form-control form-custom-user" id="name" name="name"
                                           placeholder="Nom du service"
                                           value="{{ old('name', $service->name) }}"> <!-- Préremplissage -->
                                    @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="identifier" class="form-label">Identifiant</label>
                                    <input type="text" class="form-control form-custom-user" id="identifier" name="identifier"
                                           placeholder="Identifiant unique" disabled
                                           value="{{ old('identifier', $service->identifier) }}"> <!-- Préremplissage -->
                                    @error('identifier')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control form-custom-user" id="description" name="description"
                                              placeholder="Description du service">{{ old('description', $service->description) }}</textarea> <!-- Préremplissage -->
                                    @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Bouton Soumettre -->
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn view-cart">Mettre à jour</button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal pour la suppression -->
            <div class="modal fade" id="deleteModal{{ $service->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $service->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $service->id }}">Supprimer le service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer ce service <strong>{{ $service->name }}</strong> ?</p>
                        </div>
                        <div class="modal-footer">
                            <form method="POST" action="{{ route('admin.services.destroy', $service->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal pour la modification -->
            {{-- <div class="modal fade" id="editModal{{ $service->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $service->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $service->id }}">Modifier le Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.services.update', $service->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" class="form-control form-custom-user" id="name" name="name" value="{{ $service->name }}">
                                </div>
                                <div class="mb-3">
                                    <label for="identifier" class="form-label">Identifiant</label>
                                    <input type="text" class="form-control form-custom-user" disabled id="identifier" name="identifier" value="{{ $service->identifier }}">
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control form-custom-user" id="description" name="description">{{ $service->description }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}
        @endforeach

        </div>
    <div class="row">
        <div class="col-md-6">
            <div class="pagination-container">
                {{ $services->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-translations')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>Ajouter un Service de Traduction</h3>

                    <form method="POST" action="{{ route('admin.services.store') }}">
                        @csrf
                        <div class="mb-3">

                            {{-- <label for="api_key" class="form-label">Clé API</label>
                            <input type="text" class="form-control form-custom-user" id="api_key" name="api_key"  placeholder="Clé API" value="{{ old('api_key') }}">
                            @error('api_key')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror --}}

                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control form-custom-user" id="name" name="name" placeholder="Nom du service" value="{{ old('name') }}">
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                        {{-- <div class="mb-3">
                            <label for="identifier" class="form-label">Identifiant</label>
                            <input type="text" class="form-control form-custom-user" id="identifier" name="identifier" placeholder="Identifiant unique" value="{{ old('identifier') }}">
                            @error('identifier')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div> --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control form-custom-user" id="description" name="description" placeholder="Description du service">{{ old('description') }}</textarea>
                            @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                           <!-- Bouton Soumettre -->
                           <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">Soumettre</button>
                        </div>
                    </form>
                </div>

            </div>
        @endcan
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/searchCoupon.js') }}"></script>
    <script>

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


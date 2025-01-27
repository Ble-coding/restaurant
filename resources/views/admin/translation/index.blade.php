
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Paramètres de Traduction</h1>
            <p>Configurez vos services de traduction et testez leur fonctionnement ici.</p>
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


            @foreach ($settings as $setting)
                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title">{{ ucfirst($setting->service->name) }}</h3>
                                    <div class="menu-item-dots"></div>
                                    <div class="menu-item-price">
                                        <span class="menu-badge">Clé API {{ Str::limit($setting->api_key, 25, '...') }}</span>
                                    </div>
                                </div>
                                <p class="menu-item-description">

                                    <span class="texte">
                                        Langue Source : {{ strtoupper($setting->source_lang) }}<br>
                                        Langue Cible : {{ strtoupper($setting->target_lang) }}
                                    </span>

                                    {{-- @can('edit-translations')
                                     <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $setting->id }}">✏️</a>
                                    @endcan --}}

                                    @can('edit-translations')
                                    <a href="{{ route('admin.translations.edit', $setting->id) }}" class="add_cart m-3">
                                        ✏️
                                    </a>
                                    @endcan

                                    @can('delete-translations')
                                     <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $setting->id }}">🗑️</a>
                                    @endcan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la suppression -->
                    <div class="modal fade" id="deleteModal{{ $setting->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $setting->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $setting->id }}">Supprimer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Êtes-vous sûr de vouloir supprimer la config <strong></strong> ?</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.translations.destroy', $setting->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- @endif  --}}
            @endforeach


        </div>
    <div class="row">
        <div class="col-md-6">
            <div class="pagination-container">
                {{ $settings->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-translations')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>Ajouter un Service de Traduction</h3>
                    <form method="POST" action="{{ route('admin.translations.store') }}">
                        @csrf

                        <!-- Clé API -->
                        <div class="mb-3">
                            <label for="api_key" class="form-label">Clé API</label>
                            <input type="text" class="form-control form-custom-user" id="api_key" name="api_key" placeholder="Clé API" value="{{ old('api_key') }}">
                            @error('api_key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Service de Traduction -->
                        <div class="mb-3">
                            <label for="create_service_id" class="form-label">Service de Traduction</label>
                            <select name="service_id" id="create_service_id" class="form-control form-custom-user">
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Langues -->
                        <div class="row">
                            <!-- Langue Source -->
                            <div class="col-md-6 mb-3">
                                <label for="source_lang" class="form-label">Langue Source</label>
                                <select class="form-select form-custom-user" id="source_lang" name="source_lang" onchange="updateTargetLang(this.value)">
                                    <option value="FR" {{ old('source_lang', $defaultSourceLang) === 'FR' ? 'selected' : '' }}>FR</option>
                                    <option value="EN" {{ old('source_lang', $defaultSourceLang) === 'EN' ? 'selected' : '' }}>EN</option>
                                </select>
                            </div>

                            <!-- Langue Cible -->
                            <div class="col-md-6 mb-3">
                                <label for="target_lang_display" class="form-label">Langue Cible</label>
                                <span id="target_lang_display" class="form-control  form-custom-user form-control-plaintext">{{ old('target_lang', $defaultTargetLang) }}</span>
                                <input type="hidden" id="target_lang" name="target_lang" value="{{ old('target_lang', $defaultTargetLang) }}">
                            </div>
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
        function updateTargetLang(sourceLang) {
            const targetLangInput = document.getElementById('target_lang');
            const targetLangDisplay = document.getElementById('target_lang_display');

            if (sourceLang === 'FR') {
                targetLangInput.value = 'EN';
                targetLangDisplay.textContent = 'EN';
            } else if (sourceLang === 'EN') {
                targetLangInput.value = 'FR';
                targetLangDisplay.textContent = 'FR';
            } else {
                targetLangInput.value = '';
                targetLangDisplay.textContent = '';
            }
        }

        // Initialisation de la valeur au chargement de la page
        document.addEventListener('DOMContentLoaded', function () {
            const sourceLang = document.getElementById('source_lang').value;
            updateTargetLang(sourceLang);
        });

    </script>

    {{-- <script>
        function updateEditTargetLang{{ $setting->id }}(sourceLang) {
            const targetLangInput = document.getElementById('edit_target_lang_{{ $setting->id }}');
            const targetLangDisplay = document.getElementById('edit_target_lang_display_{{ $setting->id }}');

            // Définir la langue cible automatiquement
            let targetLang = sourceLang === 'FR' ? 'EN' : 'FR';
            targetLangInput.value = targetLang;
            targetLangDisplay.textContent = targetLang;
        }
    </script> --}}


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



@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Paramètres de Traduction Edition</h1>
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
        </div>
        <div class="row">
            <div class="col-md-2">

            </div>
            @can('edit-translations')
                <div class="col-md-8">
                    <div class="cart-container-width">
                        <h3>Ajouter un Service de Traduction</h3>
                        <form method="POST" action="{{ route('admin.translations.update', $translation->id) }}">
                            @csrf
                            @method('PUT')

                            <!-- Clé API -->
                            <div class="mb-3">
                                <label for="api_key" class="form-label">Clé API</label>
                                <input type="text"
                                       class="form-control form-custom-user"
                                       id="api_key"
                                       name="api_key"
                                       placeholder="Clé API"
                                       value="{{ old('api_key', $translation->api_key) }}">
                                @error('api_key')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Service de Traduction -->
                            <div class="mb-3">
                                <label for="create_service_id" class="form-label">Service de Traduction</label>
                                <select name="service_id" id="create_service_id" class="form-control form-custom-user">
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}"
                                                {{ old('service_id', $translation->service_id) == $service->id ? 'selected' : '' }}>
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
                                    <select class="form-select form-custom-user"
                                            id="source_lang"
                                            name="source_lang"
                                            onchange="updateTargetLang(this.value)">
                                        <option value="FR" {{ old('source_lang', $translation->source_lang) === 'FR' ? 'selected' : '' }}>FR</option>
                                        <option value="EN" {{ old('source_lang', $translation->source_lang) === 'EN' ? 'selected' : '' }}>EN</option>
                                    </select>
                                </div>

                                <!-- Langue Cible -->
                                <div class="col-md-6 mb-3">
                                    <label for="target_lang_display" class="form-label">Langue Cible</label>
                                    <span id="target_lang_display"
                                          class="form-control form-custom-user form-control-plaintext">
                                        {{ old('target_lang', $translation->target_lang) }}
                                    </span>
                                    <input type="hidden"
                                           id="target_lang"
                                           name="target_lang"
                                           value="{{ old('target_lang', $translation->target_lang) }}">
                                </div>
                            </div>

                            <!-- Bouton Soumettre -->
                            <div class="row">
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn view-cart">Mettre à jour</button>
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
            @endcan

            <div class="col-md-2">

            </div>
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


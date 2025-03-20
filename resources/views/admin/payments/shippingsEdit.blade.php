
@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('shippings.title') }}</h1>
            <p>{{ __('shippings.dashboard_message') }}</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">


    <div class="row">
        <div class="col-md-2">

        </div>
        <div class="col-md-8">
            <div class="cart-container-edit">
                <h3>{{ __('shippings.edit_product') }}</h3>
                <hr>

                <form method="POST" action="{{ route('admin.shippings.update', $shipping->id) }}">

                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Champ name FR -->
                        <div class="col-md-4 mb-3">
                            <label for="name_fr" class="form-label">{{ __('shippings.name_placeholder_fr') }}</label>
                            <input type="text" class="form-control form-custom-user"
                                   name="name[fr]"
                                   value="{{ old('name.fr', $shipping->getTranslation('name', 'fr')) }}">
                            @error('name.fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ name EN -->
                        <div class="col-md-4 mb-3">
                            <label for="name_en" class="form-label">{{ __('shippings.name_placeholder_en') }}</label>
                            <input type="text" class="form-control form-custom-user"
                                   name="name[en]"
                                   value="{{ old('name.en', $shipping->getTranslation('name', 'en')) }}">
                            @error('name.en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                            <!-- Champ Prix -->
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">{{ __('shippings.price') }}</label>
                                <input type="number" class="form-control form-custom-user"
                                    name="price"
                                    value="{{ old('price', $shipping->price) }}">
                                @error('price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div class="col-md-4  mb-3">
                                <label class="form-label">{{ __('shippings.type') }}</label>
                                <select class="form-control form-select form-custom-user" name="type">
                                    <option value="free" {{ old('type', $shipping->type) == 'free' ? 'selected' : '' }}>{{ __('shippings.free') }}</option>
                                    <option value="paid" {{ old('type', $shipping->type) == 'paid' ? 'selected' : '' }}>{{ __('shippings.paid') }}</option>
                                    <option value="conditional" {{ old('type', $shipping->type) == 'conditional' ? 'selected' : '' }}>{{ __('shippings.conditional') }}</option>
                                </select>
                                @error('type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
<!-- Min Price for Free -->
<div class="col-md-8 mb-3" id="minPriceContainer" style="display: none;">
    <label>{{ __('shippings.min_price_for_free') }}</label>
    <input type="number" class="form-control form-custom-user" name="min_price_for_free" step="0.01" min="0"
        value="{{ old('min_price_for_free', $shipping->min_price_for_free) }}">
    @error('min_price_for_free')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>


                    </div>




                    <!-- Conditions -->
                    <div class="col-md-8 mb-3">
                        <label>{{ __('shippings.conditions') }}</label>
                        <div class="conditions-wrapper">
                            @php
                                $conditions = old('conditions', $shipping->conditions ?? []);
                                if (is_string($conditions)) {
                                    $conditions = json_decode($conditions, true);
                                }
                            @endphp


                            @foreach($conditions as $condition)
                                <div class="d-flex align-items-center mb-2">
                                    <input type="text" class="form-control form-custom-user" name="conditions[]" value="{{ $condition }}">
                                    <button type="button" class="btn btn-danger ms-2 remove-condition">-</button>
                                </div>

                                <div class="d-flex align-items-center mb-2">
                                    <input type="text" class="form-control form-custom-user" name="conditions[]" value="{{ $condition }}">
                                    <button type="button" class="btn btn-danger ms-2 remove-condition">-</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-primary add-condition">{{ __('shippings.add_condition') }}</button>
                        @error('conditions')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    @canany('edit-shippings')

                        <button type="submit" class="view-cart">{{ __('shippings.update_button') }}</button>

                    @endcanany
                      <!-- Bouton Soumettre -->


                </form>
            </div>
        </div>
        <div class="col-md-2">

        </div>
    </div>
</div>


@endsection

@push('scripts')

     <script src="{{ asset('assets/js/global.js') }}"></script>
     <script>
      document.addEventListener("DOMContentLoaded", function () {
    const typeSelect = document.querySelector("select[name='type']");
    const minPriceContainer = document.querySelector("#minPriceContainer");
    const conditionsWrapper = document.querySelector('.conditions-wrapper');

    // Fonction pour afficher ou cacher le champ "Min Price for Free"
    function toggleMinPriceField() {
        if (typeSelect.value === "conditional") {
            minPriceContainer.style.display = "block";
        } else {
            minPriceContainer.style.display = "none";
        }
    }

    // Appliquer l'affichage initial en fonction de la valeur actuelle du select
    toggleMinPriceField();

    // Ajouter un écouteur pour le changement du select "type"
    typeSelect.addEventListener("change", toggleMinPriceField);

    // Fonction pour ajouter un champ condition
    function addConditionField(value = "") {
        const conditionGroup = document.createElement("div");
        conditionGroup.classList.add("d-flex", "align-items-center", "mb-2");

        const input = document.createElement("input");
        input.type = "text";
        input.name = "conditions[]";
        input.classList.add("form-control", "form-custom-user");
        input.placeholder = "{{ __('shippings.conditions_placeholder') }}";
        input.value = value;

        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.classList.add("btn", "btn-danger", "ms-2");
        removeBtn.textContent = "-";

        removeBtn.addEventListener("click", function () {
            conditionGroup.remove();
        });

        conditionGroup.appendChild(input);
        conditionGroup.appendChild(removeBtn);
        conditionsWrapper.appendChild(conditionGroup);
    }

    // Ajoute un bouton "Ajouter une condition"
    const addConditionBtn = document.querySelector('.add-condition');
    addConditionBtn.addEventListener("click", function () {
        addConditionField();
    });

    // Chargement initial des conditions existantes
    const existingConditions = @json(old('conditions', is_string($shipping->conditions) ? json_decode($shipping->conditions, true) : $shipping->conditions ?? []));
    if (existingConditions && existingConditions.length > 0) {
        conditionsWrapper.innerHTML = ''; // Nettoie ce qui a été chargé par Blade
        existingConditions.forEach(condition => addConditionField(condition));
    }

    // Permet de supprimer les conditions chargées via Blade (ajoute les eventListeners)
    document.querySelectorAll('.remove-condition').forEach(btn => {
        btn.addEventListener('click', function () {
            btn.closest('div.d-flex').remove();
        });
    });
});

    </script>



@endpush


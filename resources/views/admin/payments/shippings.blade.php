
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


     <!-- Formulaire de recherche -->
     <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.shippings.index') }}" id="search-form">
                <input
                    type="text"
                    id="search"
                    class="search-input"
                    name="search"
                   placeholder="{{ __('shippings.search_placeholder') }}"
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


            @foreach ($shippings as $shipping)
                {{-- @if (!$coupon->expires_at || $coupon->expires_at > now())  --}}
                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title">{{ $shipping->getTranslation('name', app()->getLocale()) }}</h3>

                                    <div class="menu-item-dots"></div>
                                    <div class="menu-item-price">
                                        <span class="menu-badge">£{{ $shipping->price }}</span>
                                    </div>
                                </div>

                                <!-- Type de livraison -->
                                <div class="menu-item-description">
                                    <strong>{{ __('shippings.type') }}:</strong>
                                    {{ __('shippings.' . $shipping->type) }}
                                </div>

                                <!-- Prix minimum pour la livraison gratuite (affiché uniquement si le type est "conditional") -->
                                @if ($shipping->type === 'conditional' && $shipping->min_price_for_free)
                                    <div class="menu-item-description">
                                        <strong>{{ __('shippings.min_price_for_free') }}:</strong>
                                        £{{ $shipping->min_price_for_free }}
                                    </div>
                                @endif

                                <!-- Conditions (affichées uniquement si elles existent) -->
                                @if (!empty($shipping->conditions))
                                    <div class="menu-item-description">
                                        <strong>{{ __('shippings.conditions') }}:</strong>
                                        <ul class="shipping-conditions">
                                            @foreach ($shipping->conditions as $condition)
                                                <li>{{ $condition }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <p class="menu-item-description">
                                    @can('edit-shippings')
                                    <a class=" add_cart {{ Route::currentRouteName() === 'admin.shippings.edit' ? 'active' : '' }}" href="{{ route('admin.shippings.edit', $shipping->id) }}">✏️</a>
                                    @endcan
                                    @can('delete-shippings')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $shipping->id }}">🗑️</a>
                                    @endcan
                                </p>
                            </div>
                        </div>
                    </div>

  <!-- Modal pour la suppression -->
  <div class="modal fade" id="deleteModal{{ $shipping->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $shipping->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $shipping->id }}">{{ __('shippings.delete_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{!! __('shippings.delete_confirmation', ['name' => $shipping->name]) !!}</p>

            </div>
            <div class="modal-footer">
                <form method="POST" action="{{ route('admin.shippings.destroy', $shipping->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('shippings.cancel_button') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('shippings.confirm_delete') }}</button>
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
                {{ $shippings->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-shippings')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>{{ __('shippings.create_title') }}</h3>
                    <hr>
                    <form method="POST" action="{{ route('admin.shippings.store') }}">
                        @csrf

                        <!-- Champ name FR -->
                        <div class="mb-3">
                            <label for="name_fr" class="form-label">{{ __('shippings.name_placeholder_fr') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name[fr]" placeholder="{{ __('shippings.name_placeholder_fr') }}" value="{{ old('name.fr') }}">
                            @error('name.fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ name EN -->
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ __('shippings.name_placeholder_en') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name[en]" placeholder="{{ __('shippings.name_placeholder_en') }}" value="{{ old('name.en') }}">
                            @error('name.en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Prix -->
                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('shippings.price') }}</label>
                            <input type="number" class="form-control form-custom-user" name="price" placeholder="{{ __('shippings.price_placeholder') }}" value="{{ old('price') }}" min="0" step="0.01">
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">{{ __('shippings.type') }}</label>
                            <select class="form-control form-select form-custom-user" name="type">
                                <option value="free" {{ old('type') == 'free' ? 'selected' : '' }}>{{ __('shippings.free') }}</option>
                                <option value="paid" {{ old('type') == 'paid' ? 'selected' : '' }}>{{ __('shippings.paid') }}</option>
                                <option value="conditional" {{ old('type') == 'conditional' ? 'selected' : '' }}>{{ __('shippings.conditional') }}</option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Min Price for Free (optionnel) -->
                        <div class="mb-3">
                            <label for="min_price_for_free" class="form-label">{{ __('shippings.min_price_for_free') }}</label>
                            <input type="number" class="form-control form-custom-user" name="min_price_for_free" placeholder="{{ __('shippings.min_price_for_free_placeholder') }}" value="{{ old('min_price_for_free') }}" min="0" step="0.01">
                            @error('min_price_for_free')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Conditions (optionnel) -->
                        <div class="mb-3">
                            <label for="conditions" class="form-label">{{ __('shippings.conditions') }}</label>
                            <textarea class="form-control form-custom-user" name="conditions[]" placeholder="{{ __('shippings.conditions_placeholder') }}">{{ old('conditions') }}</textarea>
                            @error('conditions')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bouton Soumettre -->
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">{{ __('shippings.submit_button') }}</button>
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
     <script src="{{ asset('assets/js/global.js') }}"></script>
     <script>
        document.addEventListener("DOMContentLoaded", function () {
            const typeSelect = document.querySelector("select[name='type']");
            const minPriceContainer = document.querySelector("input[name='min_price_for_free']").closest('.mb-3');
            const conditionsContainer = document.querySelector("textarea[name='conditions[]']").closest('.mb-3');
            const conditionsWrapper = document.createElement("div");
            conditionsWrapper.classList.add("conditions-wrapper");

            // Cache le champ min_price_for_free si le type n'est pas 'conditional'
            function toggleMinPriceField() {
                if (typeSelect.value === "conditional") {
                    minPriceContainer.style.display = "block";
                } else {
                    minPriceContainer.style.display = "none";
                }
            }

            // Ajout dynamique d'une nouvelle condition
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

            // Ajoute le bouton "Ajouter une condition"
            const addConditionBtn = document.createElement("button");
            addConditionBtn.type = "button";
            addConditionBtn.classList.add("btn", "btn-primary", "mt-2");
            addConditionBtn.textContent = "{{ __('shippings.add_condition') }}";

            addConditionBtn.addEventListener("click", function () {
                addConditionField();
            });

            // Initialisation des conditions
            const existingConditions = @json(old('conditions', []));
            existingConditions.forEach(condition => addConditionField(condition));

            conditionsContainer.appendChild(conditionsWrapper);
            conditionsContainer.appendChild(addConditionBtn);

            // Appliquer les affichages initiaux
            toggleMinPriceField();

            // Écouteur d'événement sur le type de livraison
            typeSelect.addEventListener("change", toggleMinPriceField);
        });
        </script>



@endpush



@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('category.title') }}</h1>
            <p>{{ __('category.welcome_message') }}</p>
        </div>
    </div>
@endsection

@section('content')

<div class="container my-5">

    <div class="search-wrapper">
        <div class="search-container">
            <form method="GET" action="{{ route('admin.categories.index') }}" id="search-form">
                <input
                    type="text"
                    id="search"
                    class="search-input"
                    name="search"
                    placeholder="{{ __('category.search_placeholder') }}"
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


            @foreach ($categories as $category)

                    <div class="col-md-3 col-lg-6 mb-4">
                        <div class="menu-item p-3">
                            <div class="menu-item-content">
                                <div class="menu-item-header">
                                    <h3 class="menu-item-title">
                                        {{-- {{ $category->name }} --}}
                                        {{ $category->getTranslation('name', app()->getLocale()) }}
                                    </h3>
                                    <div class="menu-item-dots"></div>
                                </div>
                                <p class="menu-item-description">
                                    <span class="texte">
                                    @can('edit-categories')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">✏️</a>
                                    @endcan
                                    @can('delete-categories')
                                      <a class="add_cart m-3" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $category->id }}">🗑️</a>
                                    @endcan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la modification -->
                    <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $category->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel{{ $category->id }}">{{ __('category.edit_category', ['code' => $category->code]) }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <!-- Champ pour le libellé en français -->
                                        <div class="mb-3">
                                            <label for="name_fr" class="form-label">{{ __('category.category_fr') }}</label>
                                            <input type="text" class="form-control form-custom-user" name="name[fr]" value="{{ old('name.fr', $category->getTranslation('name', 'fr')) }}">
                                            @error('name.fr')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Champ pour le libellé en anglais -->
                                        <div class="mb-3">
                                            <label for="name_en" class="form-label">{{ __('category.category_en') }}</label>
                                            <input type="text" class="form-control form-custom-user" name="name[en]" value="{{ old('name.en', $category->getTranslation('name', 'en')) }}">
                                            @error('name.en')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn view-cart">{{ __('category.update') }}</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour la suppression -->
                    <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $category->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel{{ $category->id }}">{{ __('category.delete_category') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ __('category.delete_confirmation', ['name' => $category->name]) }}</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('category.cancel') }}</button>
                                        <button type="submit" class="btn btn-danger">{{ __('category.delete') }}</button>
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
                {{ $categories->links('vendor.pagination.custom') }}
            </div>
        </div>
        @can('create-categories')
            <div class="col-md-6">
                <div class="cart-container-width">
                    <h3>{{ __('category.create_title') }}</h3>
                    <hr>
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf

                        <!-- Champ Code -->
                        {{-- <div class="mb-3">
                            <label for="name" class="form-label">Catégorie</label>
                            <input type="text" class="form-control form-custom-user" name="name" placeholder="Libellé" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}

                        <div class="mb-3">
                            <label for="name_fr" class="form-label">{{ __('category.category_fr') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name[fr]" placeholder="{{ __('category.label_fr') }}" value="{{ old('name.fr') }}">
                            @error('name.fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Champ pour la traduction en anglais -->
                        <div class="mb-3">
                            <label for="name_en" class="form-label">{{ __('category.category_en') }}</label>
                            <input type="text" class="form-control form-custom-user" name="name[en]" placeholder="{{ __('category.label_en') }}" value="{{ old('name.en') }}">
                            @error('name.en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bouton Soumettre -->
                        <div class="cart-actions mt-4">
                            <button type="submit" class="view-cart">{{ __('category.submit') }}</button>
                        </div>
                    </form>
                </div>

            </div>
        @endcan
    </div>
</div>

@endsection

@push('scripts')

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

    <script src="{{ asset('assets/js/search.js') }}"></script>
@endpush


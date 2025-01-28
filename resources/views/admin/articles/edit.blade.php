@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('blog.edit_title') }}</h1>
            <p>{{ __('blog.dashboard_message') }}</p>
        </div>
    </div>
@endsection

@push('styles')
    <!-- CSS de Summernote -->
    <link href="{{ asset('assets/css/editors/summernote.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container my-5">
        <div class="cart-container-width-summernote">
            <!-- Formulaire d'édition d'article -->
            <form method="POST" action="{{ route('admin.articles.update', $article->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Colonne 1 : Titre, Slug, Catégorie, Image -->
                    <div class="col-md-4">
                        <h1>{{ __('blog.edit_title') }}</h1>

                        <div class="mb-3">
                            <label for="title_en" class="form-label">{{ __('blog.english_title') }}</label>
                            <input type="text" name="title_en" id="title_en"
                                   class="form-control form-custom-user"
                                   placeholder="{{ __('blog.english_title') }}"
                                   value="{{ old('title_en', $article->title_en) }}">
                            @error('title_en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="title_fr" class="form-label">{{ __('blog.french_title') }}</label>
                            <input type="text" name="title_fr" id="title_fr"
                                   class="form-control form-custom-user"
                                   placeholder="{{ __('blog.french_title') }}"
                                   value="{{ old('title_fr', $article->title_fr) }}">
                            @error('title_fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">{{ __('blog.category_label') }}</label>
                            <select name="category_id" id="category_id" class="form-select form-custom-user">
                                <option value="">{{ __('blog.select_status') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                            {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">{{ __('blog.image_label') }}</label>
                            <input type="file" class="form-control form-custom-user me-2"
                                   name="image" id="image" accept="image/*">
                            @if($article->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ __('blog.image_label') }}" style="max-width: 100%; height: auto;">
                                </div>
                            @endif
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('blog.status_label') }}</label>
                            <select id="status" name="status" class="form-select form-custom-user">
                                <option value="" disabled selected>{{ __('blog.select_status') }}</option>
                                @foreach ($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $article->status) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- Colonne 2 : Contenu (Summernote) -->
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label" for="content_en">{{ __('blog.content_en_label') }}</label>
                            <textarea id="summernote_en" name="content_en" class="form-control form-custom-user">
                                {{ old('content_en', $article->content_en) }}
                            </textarea>
                            @error('content_en')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="content_fr">{{ __('blog.content_fr_label') }}</label>
                            <textarea id="summernote" name="content_fr" class="form-control form-custom-user">
                                {{ old('content_fr', $article->content_fr) }}
                            </textarea>
                            @error('content_fr')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Bouton Soumettre -->
                    <div class="cart-actions mt-4">
                        @canany(['edit-articles', 'edit-blogs'])
                            <button type="submit" class="btn btn-primary view-cart">{{ __('blog.update_button') }}</button>
                        @endcanany
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS de Summernote -->
    <script src="{{ asset('assets/js/summernote/editors.js') }}"></script>
    <script src="{{ asset('assets/js/summernote/editorsCreate.js') }}"></script>
@endpush

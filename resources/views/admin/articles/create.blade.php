@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>{{ __('blog.create_title') }}</h1>
            <p>{{ __('blog.welcome_message') }}</p>
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

                <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Colonne 1 : Titre, Slug, Catégorie, Image -->
                        <div class="col-md-4">
                            {{-- <h5>{{ __('blog.title_en') }}</h5> --}}
                            <div class="mb-3">
                                <label for="title_en" class="form-label">{{ __('blog.title_en') }}</label>
                                <input type="text" name="title_en" id="title_en" class="form-control form-custom-user" placeholder="Resource title">
                                @error('title_en')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                    
                            {{-- <h5>{{ __('blog.title_fr') }}</h5> --}}
                            <div class="mb-3">
                                <label for="title_fr" class="form-label">{{ __('blog.title_fr') }}</label>
                                <input type="text" name="title_fr" id="title_fr" class="form-control form-custom-user" placeholder="Titre de la ressource">
                                @error('title_fr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                    
                            <div class="mb-3">
                                <label for="category_id" class="form-label">{{ __('blog.category') }}</label>
                                <select name="category_id" id="category_id" class="form-select form-custom-user">
                                    <option value="">-- {{ __('blog.select_status') }} --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                    
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('blog.status') }}</label>
                                <select id="status" name="status" class="form-select form-custom-user">
                                    <option value="" disabled selected>-- {{ __('blog.select_status') }} --</option>
                                    @foreach (\App\Models\Blog::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                            {{ trans('blog.statuses.' . $key) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    
                        <!-- Colonne 2 : Contenu -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label" for="content_en">{{ __('blog.content_en') }}</label>
                                <textarea id="summernote_en" name="content_en" class="form-control form-custom-user"></textarea>
                                @error('content_en')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="content_fr">{{ __('blog.content_fr') }}</label>
                                <textarea id="summernote" name="content_fr" class="form-control form-custom-user"></textarea>
                                @error('content_fr')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    
                        <!-- Bouton Soumettre -->
                        <div class="cart-actions mt-4">
                            @canany(['create-articles', 'create-blogs'])
                            <button type="submit" class="btn btn-primary view-cart">{{ __('blog.submit') }}</button>
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

@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Blogs / Edit</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
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
                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" name="title" id="title"
                                   class="form-control form-custom-user"
                                   placeholder="Titre de la ressource"
                                   value="{{ old('title', $article->title) }}">
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Catégorie -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Catégorie</label>
                            <select name="category_id" id="category_id" class="form-select form-custom-user">
                                <option value="">-- Sélectionnez une catégorie --</option>
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

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Image de l'article</label>
                            <input type="file" class="form-control form-custom-user me-2"
                                   name="image" id="image" accept="image/*">
                            @if($article->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $article->image) }}" alt="Image actuelle" style="max-width: 100%; height: auto;">
                                </div>
                            @endif
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut de l'article</label>
                            <select id="status" name="status" class="form-select form-custom-user">
                                <option value="">-- Sélectionnez un statut --</option>
                                @foreach (\App\Models\Blog::STATUSES as $key => $label)
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
                            <label class="form-label" for="content">Contenu</label>
                            <textarea id="summernote" name="content" class="form-control form-custom-user">
                                {{ old('content', $article->content) }}
                            </textarea>
                            @error('content')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    <!-- Bouton Soumettre -->
                    <div class="cart-actions mt-4">
                        <button type="submit" class="btn btn-primary view-cart">Mettre à jour</button>
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

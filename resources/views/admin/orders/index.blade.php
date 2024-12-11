


@extends('layouts.masterAdmin')

@section('headerContent')
    <div class="main-section">
        <div class="container text-center">
            <h1>Commandes</h1>
            <p>Bienvenue dans le tableau de bord, votre centre de contrôle où vous pouvez consulter les informations importantes et gérer vos paramètres.</p>
        </div>
    </div>
@endsection

@section('content')


    <div class="container my-5">

        <div class="search-wrapper mb-4">
            <div class="search-container">
                <form method="GET" action="{{ route('admin.commandes.index') }}" id="search-form" class="d-flex">
                    <input
                        type="text"
                        id="search"
                        class="form-control form-custom-user search-input"
                        name="search"
                        placeholder="Rechercher par produit..."
                        value="{{ request()->get('search') }}"
                    >
                    {{-- <button type="submit" class="btn view-cart ms-2">Rechercher</button> --}}
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
            <!-- Début des items de menu -->
            <div class="row">
                @if($orders->isEmpty())
                    <p>Aucune commande trouvée.</p>
                @else
                    @foreach ($orders as $order)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="menu-item p-3">
                                <div class="menu-item-content">
                                    <div class="menu-item-header">

                                        <h3 class="menu-item-title">
                                            {{ $order->first_name }} {{ $order->last_name }}
                                        </h3>

                                        <div class="menu-item-dots"></div>

                                        <div class="menu-item-price">
                                            £ {{ number_format($order->total, 2) }}
                                        </div>
                                    </div>

                                    <p class="menu-item-description">
                                        <span class="menu-badge">
                                            {{ $order->created_at->translatedFormat('j F, Y') }} -
                                            ({{ $order->getStatusLabel() }})</span>


                        <!-- Afficher le bouton de modification seulement pour les statuts modifiables -->

                            @unless(in_array($order->status, ['shipped', 'delivered', 'canceled']))
                                <a href="#" class="add_cart m-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $order->id }}">✏️</a>
                            @endunless

                            <a class="{{ Route::currentRouteName() === 'admin.commandes.show' ? 'active' : '' }}" href="{{ route('admin.commandes.show', $order->id) }}">👀</a>

                                    </p>

                                    <ul class="menu-item-products">
                                        @php
                                            $productCount = $order->products->sum('pivot.quantity');
                                        @endphp

                                        <strong> {{ $order->code }}  {{ \Illuminate\Support\Str::plural('Produit', $productCount) }} : {{ $productCount }}</strong>

                                        {{-- @foreach($order->products as $product)
                                            <li>{{ $product->name }} (x{{ $product->pivot->quantity }})</li>
                                        @endforeach --}}

                                        {{-- <strong class="compte"> Id compte : {{ $order->customer->first_name }} {{ $order->customer->last_name }}</strong> --}}
                                    </ul>

                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editModal{{ $order->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $order->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel{{ $order->id }}">Modifier la Commande #{{ $order->code }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('admin.commandes.update', $order->id) }}">
                                            @csrf
                                            @method('PATCH')

                                            <div class="mb-3">
                                                <label for="status" class="form-label">Statut</label>
                                                <select name="status" class="form-select">
                                                    @foreach(App\Models\Order::STATUSES as $key => $label)
                                                        <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <button type="submit" class="btn view-cart">Mettre à jour</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                    @endforeach
                @endif
            </div>

            <!-- Fin des items de menu -->
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="pagination-container">
                    {{ $orders->links('vendor.pagination.custom') }}
                </div>
            </div>

        </div>


    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/search.js') }}"></script>
@endpush

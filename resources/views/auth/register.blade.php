<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrement | Drinks & Foods Côte d'Ivoire</title>
    <link rel="icon" href="{{ asset('assets/images/logo_png.ico') }}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
    <!-- CSS de intl-tel-input -->

    <!-- Bootstrap Icons -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    {{-- <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script> --}}

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">


</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <!-- Welcome Section -->
            <div class="col-12 col-md-6 mb-4">
                <div class="welcome-box p-4">
                    <h1><span class="logo-highlight">Côte d'Ivoire</span> Drinks & Foods</h1>
                    <p>Découvrez les saveurs authentiques de la Côte d’Ivoire directement à Londres !</p>
                    <div class="social-icons d-flex justify-content-start">
                        <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-google"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>

            <!-- Register Form -->
            <div class="col-12 col-md-6">
                <div class="login-box p-4">
                    <h3>S'inscrire</h3>
                    <form method="POST" action="{{ route('register.account') }}">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Nom Complet et Email -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom Complet</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                    class="form-control form-custom-user @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Adresse e-mail</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="form-control form-custom-user @error('email') is-invalid @enderror" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Téléphone et Rôle -->
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Numéro de téléphone</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                    class="form-control form-custom-user @error('phone') is-invalid @enderror"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    required>

                                    <input type="hidden" id="country_code" name="country_code">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Rôle</label>
                                <select id="role" name="role" class="form-select form-custom-user" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Mot de passe et Confirmation -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" id="password" name="password"
                                    class="form-control form-custom-user @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmez le mot de passe</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control form-custom-user @error('password_confirmation') is-invalid @enderror"
                                    required>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Bouton S'inscrire -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="view-cart">
                                <a class="text-decoration-none" href="{{ route('login') }}">Déjà enregistré ?</a>
                            </div>
                            <button type="submit" class="btn btn-login">S'inscrire</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
     const phoneInput = document.querySelector("#phone");
        const countryCodeInput = document.querySelector("#country_code");

        const iti = intlTelInput(phoneInput, {
            initialCountry: "fr", // Default country
            preferredCountries: ["fr", "us", "gb"], // Preferred countries
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        // Lorsque l'utilisateur modifie le pays, on met à jour le champ caché avec le code du pays
        phoneInput.addEventListener("input", function() {
            countryCodeInput.value = iti.getSelectedCountryData().dialCode; // Capture le code du pays sélectionné
        });


    </script>

    {{-- <script src="{{ asset('assets/js/flag.js') }}"></script> --}}
    <script>
        $(document).ready(function() {
            $('#role').select2({
                theme: 'bootstrap4',
                width: '100%' // Assure que Select2 prend 100% de la largeur du conteneur parent
            });
        });
    </script>

</body>
</html>


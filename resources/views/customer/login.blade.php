<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | Drinks & Foods Côte d'Ivoire</title>
    <link rel="icon" href="{{ asset('assets/images/logo_png.ico') }}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <!-- Bootstrap Icons -->
    <link href="{{ asset('assets/bootstrap/css/bootstrap-icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
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

            <!-- Login Form -->
            <div class="col-12 col-md-6">
              <div class="login-box p-4">
                <h3>Se connecter</h3>
                <form method="POST" action="{{ route('customer.login') }}">
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

                    <!-- Email or Phone -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail </label>
                        <input type="text" id="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror
                            form-custom-user" placeholder="demo@gmail.com" required autofocus>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror
                            form-custom-user" placeholder="*********" required>
                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>



                    <!-- Login Button -->
                    <button type="submit" class="btn btn-login w-100">Se connecter</button>
                </form>

                <p class="register-link mt-4 text-center">
                    Pas encore de compte ? <a href="{{ route('customer.register') }}">Inscrivez-vous ici</a>
                </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
</body>
</html>


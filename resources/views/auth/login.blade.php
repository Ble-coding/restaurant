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
                <form method="POST" action="{{ route('login') }}">
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
                        <label for="login" class="form-label">Adresse e-mail ou Téléphone</label>
                        <input type="text" id="login" name="login" value="{{ old('login') }}"
                            class="form-control @error('login') is-invalid @enderror
                            form-custom-user" placeholder="demo@gmail.com" required autofocus>
                        @error('login')
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

                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-custom-user" id="remember_me" name="remember" class="form-check-input">
                            <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
                        </div>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-password">Mot de passe oublié ?</a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="btn btn-login w-100">Se connecter</button>
                </form>

                <p class="register-link mt-4 text-center">
                    Pas encore de compte ? <a href="{{ route('register.account') }}">Inscrivez-vous ici</a>
                </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
</body>
</html>


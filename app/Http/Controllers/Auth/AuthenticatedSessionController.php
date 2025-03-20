<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(route('dashboard', absolute: false));
    // }

    public function store(Request $request)
    {
        // Validation des champs
        $credentials = $request->validate([
            'login' => 'required|string', // Accepte email ou téléphone
            'password' => 'required|string',
        ]);

        // Identifier si l'entrée est un email ou un numéro de téléphone
        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Tentative de connexion
        if (Auth::attempt([$loginType => $credentials['login'], 'password' => $credentials['password']])) {
            $request->session()->regenerate(); // Réinitialise la session pour des raisons de sécurité
            return redirect()->intended(route('dashboard', absolute: false)); // Redirection vers la page cible
        }

        // Si la tentative échoue
        return back()->withErrors([
            'login' => 'Les informations de connexion sont incorrectes.',
        ])->withInput();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // return redirect('/');
        return redirect()->route('home');
    }
}

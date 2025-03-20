<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login'); // Vue unique pour la connexion
    }

    public function login(Request $request)
    {
        // Identifier si l'entrée est un email ou un numéro de téléphone
        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = $request->validate([
            'login' => 'required',  // Peut être email ou téléphone
            'password' => 'required|min:6',
        ]);

        // Tentative de connexion pour Admin (web)
        if ($loginField === 'email' && Auth::guard('web')->attempt([
            'email' => $credentials['login'],
            'password' => $request->password
        ])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Connexion Admin réussie.');
        }

        // Tentative de connexion pour Client (customer)
        if (Auth::guard('customer')->attempt([
            $loginField => $credentials['login'],
            'password' => $request->password
        ])) {
            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Connexion Client réussie.');
        }

        // Si aucune tentative ne réussit
        throw ValidationException::withMessages([
            'login' => ['Les informations de connexion sont incorrectes.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home'); // Redirection vers l'accueil
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

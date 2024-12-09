<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
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

    // Affiche le formulaire de connexion
    public function showLoginForm()
    {
        return view('customer.login');
    }

    // Affiche le formulaire d'enregistrement
    public function showRegisterForm()
    {
        return view('customer.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6|confirmed',
            'country_code' => 'required|string|max:255',
            'phone' => ['required', 'string', 'digits_between:8,15', 'unique:customers,phone'],
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.login')->with('success', 'Votre compte a été créé. Connectez-vous !');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('customer')->attempt($request->only('email', 'password'))) {
            return redirect()->intended('/')->with('success', 'Connexion réussie !');
        }

        return back()->withErrors(['email' => 'Les informations d’identification sont incorrectes.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        return redirect()->route('customer.login')->with('success', 'Déconnexion réussie.');
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

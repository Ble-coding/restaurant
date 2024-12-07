<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::select('id', 'name')->get(); // Sélectionne les rôles
        return view('auth.register', compact('roles'));
    }
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //         'phone' => ['required', 'string', 'max:15', 'unique:'.User::class],
    //         'role' => ['required', Rule::in($roles)],
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     $user->assignRole($validatedData['role']);
    //     event(new Registered($user));

    //     Auth::login($user);


    //     // return redirect(RouteServiceProvider::HOME);
    // }

    public function store(Request $request): RedirectResponse
    {
       // Récupère les noms des rôles disponibles
      // $roleNames = Role::pluck('name')->toArray();




       $request->validate([
           'name' => ['required', 'string', 'max:255'],
           'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
           'password' => ['required', 'confirmed', Rules\Password::defaults()],
           'phone' => ['required', 'string', 'max:15', 'digits_between:8,15', 'unique:'.User::class],
            //    'role' => ['required', Rule::in($roleNames)],
            'country_code' => ['required', 'string'],  // Valider le code du pays
            'role' => ['required', Rule::in(Role::pluck('name')->toArray())],
       ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
             'country_code' => $request->country_code,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role); // Correction pour $request->role
        event(new Registered($user));

        Auth::login($user);

        // return redirect()->route('dashboard');
        return redirect(route('dashboard', absolute: false));
        // return redirect(RouteServiceProvider::HOME);
    }
}

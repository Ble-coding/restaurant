<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Nettoyer et récupérer le terme de recherche depuis la requête GET
        $search = trim($request->get('search'));

        // Récupérer les utilisateurs avec leurs rôles, en excluant 'Admin' et 'Super Admin'
        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['Admin', 'Super Admin']); // Exclusion de certains rôles
            })
            ->when($search, function ($query) use ($search) {
                // Appliquer la recherche sur les champs 'name' et 'email'
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc') // Trier par date de création
            ->paginate(10); // Paginer les résultats (10 par page)

        // Charger tous les rôles disponibles
        $roles = Role::all();

        // Retourner la vue avec les utilisateurs et les rôles
        return view('admin.users.index', compact('users', 'roles'));
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'digits_between:8,15', 'unique:users,phone'],
            'country_code' => ['required', 'string'],
            'roles' => ['required', 'array'], // Rôle doit être un tableau
            'roles.*' => ['exists:roles,name'], // Assurez-vous que les rôles existent dans la base de données
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'password' => bcrypt($request->password), // Hash du mot de passe
        ]);

        // Assigner plusieurs rôles
        $user->assignRole($request->roles); // Assigner les rôles passés dans la requête

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé avec succès.');
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
    public function update(Request $request, User $user): RedirectResponse
    {
        // Validation des données
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'digits_between:8,15', 'unique:users,phone,' . $user->id],
            'country_code' => ['required', 'string'],
            'roles' => ['required', 'array'], // Le champ 'roles' doit être un tableau
            'roles.*' => ['exists:roles,name'], // Vérifie que chaque rôle existe
            'password' => ['nullable', 'confirmed', Password::defaults()]
        ]);

        // Mise à jour des informations utilisateur
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
        ]);

          // Mise à jour du mot de passe si fourni
        if ($request->password) {
            $user->password = bcrypt($request->password); // Vous pouvez utiliser un autre mécanisme pour hasher le mot de passe
            $user->save();
        }


        // Mettre à jour les rôles de l'utilisateur
        $user->syncRoles($request->roles); // Synchroniser avec les rôles reçus dans la requête

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Supprimer l'utilisateur
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

}

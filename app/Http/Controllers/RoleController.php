<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use App\Models\Permission;
use Illuminate\Support\Facades\Gate;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if (!auth()->user()->can('view-roles')) {
            abort(403, __('role.forbidden'));
        }
        // Nettoyer et récupérer le terme de recherche depuis la requête GET
        $search = trim($request->get('search'));

        // Détermine les rôles à exclure selon le rôle de l'utilisateur connecté
        $excludedRoles = [];
        if (auth()->user()->hasRole('super_administrator')) {
            $excludedRoles = ['super_administrator'];
        } elseif (auth()->user()->hasRole('administrator')) {
            $excludedRoles = ['administrator', 'super_administrator']; // Exclure admin et super_admin
        }

        // Récupérer tous les permissions disponibles
        $permissions = Permission::all();

        // Récupérer les rôles avec leurs permissions, appliquer le filtrage et exclure 'Admin'
        $roles = Role::with('permissions')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%') // Rechercher dans le nom
                             ->orWhere('guard_name', 'like', '%' . $search . '%') // Rechercher dans guard_name
                             ->orWhere('name_fr', 'like', '%' . $search . '%')
                             ->orWhere('name_en', 'like', '%' . $search . '%'); // Rechercher dans translation (si ce champ existe)
                });
            })
            ->whereNotIn('name', $excludedRoles)
            ->orderBy('created_at', 'desc') // Trier par date de création descendante
            ->paginate(5); // Pagination des résultats (5 par page)

        // Retourner la vue avec les données
        return view('admin.roles.index', compact('roles', 'permissions'));
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
    // public function store(Request $request)
    // {
    //     // Valider les données
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255|min:3|unique:roles,name',
    //         'permissions' => 'array|required', // Les permissions doivent être un tableau
    //         'permissions.*' => 'integer|exists:permissions,id', // Chaque permission    doit    exister
    //     ]);

    //     // Convertir le nom en minuscule
    //     $name = strtolower($validatedData['name']);

    //     // Déterminer le `guard_name` et la traduction (exemple à conserver si pertinent)
    //     $guardMapping = $this->getGuardMapping();
    //     $guardName = $guardMapping[$name]['guard'] ?? 'web';
    //     $translation = $guardMapping[$name]['translation'] ?? ucfirst($name);

    //     // Créer le rôle
    //     $role = Role::create([
    //         'name' => $name,
    //         'guard_name' => $guardName,
    //         'translation' => $translation,
    //     ]);

    //     // Attribuer les permissions sélectionnées
    //     $permissions = Permission::whereIn('id', $validatedData['permissions'])->get();
    //     $role->givePermissionTo($permissions);

    //     // Rediriger avec un message de succès
    //     return redirect()->route('admin.roles.index')->with('success', 'Rôle créé avec succès, avec permissions assignées.');
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valider les données
        $validatedData = $request->validate([
            'name_fr' => 'required|string|max:255|min:3', // Validation pour le nom en français
            'name_en' => 'required|string|max:255|min:3', // Validation pour le nom en anglais
            'permissions' => 'array|required', // Les permissions doivent être un tableau
            'permissions.*' => 'integer|exists:permissions,id', // Chaque permission doit exister
        ]);

        // Générer le slug à partir de la traduction principale (par exemple, anglais)
        $slug = strtolower(str_replace(' ', '-', $validatedData['name_en']));

        // Créer le rôle
        $role = Role::create([
            'name_fr' => $validatedData['name_fr'], // Stocké sous format normal
            'name_en' => $validatedData['name_en'], // Stocké sous format normal
            'guard_name' => 'web', // Par défaut
            'name' => $slug,
            'user_id' => auth()->id(),
        ]);

        // Attribuer les permissions sélectionnées
        $permissions = Permission::whereIn('id', $validatedData['permissions'])->get();
        $role->givePermissionTo($permissions);

        // Rediriger avec un message de succès
        return redirect()->route('admin.roles.index')
        ->with('success', 'Rôle créé avec succès, avec permissions assignées.');
    }



    /**
     * Display the specified resource.
     */

     public function show(Role $role)
     {
         // Affiche un rôle spécifique
         return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Affiche le formulaire pour modifier un rôle
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Role $role)
    // {
    //     // Valider les données
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255|min:3|unique:roles,name,' . $role->id, // Ne pas inclure la colonne 'id'
    //         // 'name' => 'required|string|max:255|min:3|unique:roles,name,' . $role->id . ',id',
    //         'permissions' => 'array|required', // Les permissions doivent être un tableau
    //         'permissions.*' => 'integer|exists:permissions,id', // Chaque permission doit exister
    //     ]);

    //     // Convertir le nom en minuscule
    //     $name = strtolower($validatedData['name']);

    //     // Déterminer le `guard_name` et la traduction (garder les anciens si non modifiables)
    //     $guardMapping = $this->getGuardMapping();
    //     $guardName = $guardMapping[$name]['guard'] ?? $role->guard_name;
    //     $translation = $guardMapping[$name]['translation'] ?? ucfirst($name);

    //     // Mettre à jour les données du rôle
    //     $role->update([
    //         'name' => ucfirst($name), // Capitaliser
    //         'guard_name' => $guardName,
    //         'translation' => $translation,
    //     ]);

    //     // Synchroniser les permissions sélectionnées
    //     $permissions = Permission::whereIn('id', $validatedData['permissions'])->get();
    //     $role->syncPermissions($permissions);

    //     // Rediriger avec un message de succès
    //     return redirect()->route('admin.roles.index')->with('success', 'Rôle mis à jour avec succès, avec permissions assignées.');
    // }



    // public function update(Request $request, Role $role)
    // {
    //     // Affichage de la demande pour la déboguer
    //     \Log::info("Validation Request Data", $request->all());

    //     // Valider les données
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255|min:3|unique:roles,name,' . $role->id, // Exclure l'ID actuel lors de la vérification d'unicité
    //         'permissions' => 'array|required', // Les permissions doivent être un tableau
    //         'permissions.*' => 'integer|exists:permissions,id', // Chaque permission doit exister
    //     ]);

    //     \Log::info("Validated Data", $validatedData);

    //     // Convertir le nom en minuscule
    //     $name = strtolower($validatedData['name']);

    //     // Déterminer le `guard_name` et la traduction (garder les anciens si non modifiables)
    //     $guardMapping = $this->getGuardMapping();
    //     $guardName = $guardMapping[$name]['guard'] ?? $role->guard_name;
    //     $translation = $guardMapping[$name]['translation'] ?? ucfirst($name);

    //     // Mettre à jour les données du rôle
    //     $role->update([
    //         'name' => $name,
    //         'guard_name' => $guardName,
    //         'translation' => $translation,
    //     ]);

    //     // Synchroniser les permissions sélectionnées
    //     $permissions = Permission::whereIn('id', $validatedData['permissions'])->get();
    //     $role->syncPermissions($permissions);

    //     // Rediriger avec un message de succès
    //     return redirect()->route('admin.roles.index')->with('success', 'Rôle mis à jour avec succès, avec permissions assignées.');
    // }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Valider les données
        $validatedData = $request->validate([
            'name_fr' => 'required|string|max:255|min:3', // Nom en français obligatoire
            'name_en' => 'required|string|max:255|min:3', // Nom en anglais obligatoire
            'permissions' => 'array|required', // Les permissions doivent être un tableau
            'permissions.*' => 'integer|exists:permissions,id', // Chaque permission doit exister
        ]);

        // Générer le slug à partir de la version anglaise (ou française si indisponible)
        $slug = strtolower(str_replace(' ', '-', $validatedData['name_en']));

        // Mettre à jour les données du rôle
        $role->update([
            'name_fr' => $validatedData['name_fr'], // Mise à jour du nom en français
            'name_en' => $validatedData['name_en'], // Mise à jour du nom en anglais
            'name' => $slug, // Mettre à jour le slug
            'guard_name' => $role->guard_name, // Conserver la valeur existante
            'user_id' => auth()->id(),
        ]);

        // Synchroniser les permissions sélectionnées
        $permissions = Permission::whereIn('id', $validatedData['permissions'])->get();
        $role->syncPermissions($permissions);

        // Rediriger avec un message de succès
        return redirect()->route('admin.roles.index')->with('success', 'Rôle mis à jour avec succès, avec permissions assignées.');
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Supprime un rôle
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Rôle supprimé avec succès');
    }



    // public function givePermission(Request $request, Role $role)
    // {
    //     if($role->hasPermissionTo($request->permission)){
    //         return back()->with('success', 'Permission exits');
    //     }
    //     $role->givePermission($request->permission);
    //     return back()->with('success', 'Permission added');
    // }
    public function givePermission(Request $request, Role $role)
    {
        $request->validate([
            'permission' => 'required|exists:permissions,name', // Valide que la permission existe
        ]);

        if ($role->hasPermissionTo($request->permission)) {
            return back()->with('success', 'Permission already exists.');
        }

        $role->givePermissionTo($request->permission);
        return back()->with('success', 'Permission added successfully.');
    }


     /**
     * Retourne le tableau de correspondance pour les rôles.
     */
    private function getGuardMapping(): array
    {
        // Récupérer les rôles depuis la base de données
        $roles = \DB::table('roles')->get(['name', 'guard_name']);

        // Si des rôles existent dans la base de données, les mapper dynamiquement
        if ($roles->isNotEmpty()) {
            return $roles->mapWithKeys(function ($role) {
                return [
                    $role->name => [
                        'guard' => $role->guard_name,
                        'translation' => ucfirst($role->name), // Par défaut : capitalisation du nom
                    ],
                ];
            })->toArray();
        }

        // Si la table des rôles est vide, retourner le tableau par défaut
        return [
            'admin' => ['guard' => 'web', 'translation' => 'Administrateur'],
            'editor' => ['guard' => 'web', 'translation' => 'Éditeur'],
            'user' => ['guard' => 'web', 'translation' => 'Utilisateur'],
            'moderator' => ['guard' => 'api', 'translation' => 'Modérateur'],
            'manager' => ['guard' => 'web', 'translation' => 'Gestionnaire'],
            'customer' => ['guard' => 'api', 'translation' => 'Client'],
            'seller' => ['guard' => 'api', 'translation' => 'Vendeur'],
            'support' => ['guard' => 'web', 'translation' => 'Support technique'],
            'developer' => ['guard' => 'web', 'translation' => 'Développeur'],
            'guest' => ['guard' => 'web', 'translation' => 'Invité'],
        ];
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
// use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Artisan;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if (!auth()->user()->can('view-permissions')) {
            abort(403, 'Vous n\'avez pas la permission de voir cette page.');
        }
        // Nettoyer et récupérer le terme de recherche depuis la requête GET
        $search = trim($request->get('search'));

        // Appliquer le filtrage sur les permissions si un terme de recherche est présent
        $permissions = Permission::when($search, function ($query, $search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', '%' . $search . '%') // Rechercher dans le nom
                         ->orWhere('guard_name', 'like', '%' . $search . '%') // Rechercher dans guard_name
                         ->orWhere('translation', 'like', '%' . $search . '%'); // Rechercher dans translation
            });
        })
        ->orderBy('created_at', 'desc') // Trier par date de création descendante
        ->paginate(6); // Pagination avec 6 permissions par page

        // Retourner la vue avec les données paginées
        return view('admin.permissions.index', compact('permissions'));
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
    //     $validatedData = $request->validate([
    //         // 'name' => 'required|string|max:255|min:3|unique:roles,name',
    //         'action' => 'required|string|in:create,view,edit,delete',
    //         'resource' => 'required|string|in:' . implode(',', Permission::getResources()),
    //     ]);

    //     // Force lowercase
    //     // $name = strtolower($validatedData['name']);
    //     $name = strtolower($validatedData['action'] . '-' . $validatedData['resource']);
    //     if (Permission::where('name', $name)->exists()) {
    //         return redirect()->back()->withErrors(['name' => 'Cette permission existe déjà.']);
    //     }

    //     // Obtenir les correspondances guard_name et translation
    //     $guardMapping = $this->getPermissionMapping();

    //     // Forcer le nom en minuscule pour correspondre à `$guardMapping`
    //     $name = strtolower($validatedData['name']);

    //     // Obtenir `guard_name` et `translation`
    //      // Fetch values
    //     $guardName = $guardMapping[$name]['guard'] ?? 'web'; // Default: web
    //     $translation = $guardMapping[$name]['translation'] ?? ucfirst($name); // Par défaut : ucfirst du nom


    //     \Log::info("Guard Name: $guardName, Translation: $translation");
    //     // Debugging optionnel pour vérifier si `translation` est correcte
    //     if (is_null($translation)) {
    //         throw new \Exception("La traduction pour la permission '{$name}' est introuvable.");
    //     }

    //     // Créer la permission avec les informations
    //     Permission::create([
    //         'name' => $name,
    //         'guard_name' => $guardName,    // Correspondance ou valeur par défaut
    //         'translation' => $translation, // Traduction ou valeur par défaut
    //     ]);

    //     // Rediriger avec message de succès
    //     return redirect()->route('admin.permissions.index')->with('success', 'Permission créée avec succès');
    // }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'action' => 'required|string|in:create,view,edit,delete',
            'resource' => 'required|string|in:' . implode(',', Permission::getResources()),
        ]);

        // Construit le nom de la permission
        $name = strtolower($validatedData['action'] . '-' . $validatedData['resource']);

        // Vérifie si la permission existe déjà
        if (Permission::where('name', $name)->exists()) {
            return redirect()->back()->withErrors(['name' => 'Cette permission existe déjà.']);
        }

        // Obtenir les correspondances `guard_name` et `translation`
        $guardMapping = $this->getPermissionMapping();
        $guardName = $guardMapping[$name]['guard'] ?? 'web'; // Valeur par défaut : 'web'
        $translation = $guardMapping[$name]['translation'] ?? ucfirst($name); // Valeur par défaut : ucfirst

        \Log::info("Guard Name: $guardName, Translation: $translation");

        // Crée la permission
        Permission::create([
            'name' => $name,
            'guard_name' => $guardName,
            'translation' => $translation,
        ]);

        // Redirige avec un message de succès
        return redirect()->route('admin.permissions.index')->with('success', 'Permission créée avec succès.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {

        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Permission $permission)
    // {

    //              // Validation des données d'entrée
    //              $validatedData = $request->validate([
    //                 'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id, // Ignorer la validation de l'unicité pour le rôle actuel
    //             ]);

    //             // Forcer le nom en minuscule
    //             $name = strtolower($validatedData['name']);

    //             // Obtenir les correspondances guard_name et translation
    //             $guardMapping = $this->getPermissionMapping();

    //             // Obtenir `guard_name` et `translation`
    //             $guardName = $guardMapping[$name]['guard'] ?? 'web'; // Valeur par défaut: 'web'
    //             $translation = $guardMapping[$name]['translation'] ?? ucfirst($name); // Valeur par défaut : ucfirst($name)

    //             // Debugging optionnel pour vérifier si `translation` est correcte
    //             \Log::info("Guard Name: $guardName, Translation: $translation");

    //             if (is_null($translation)) {
    //                 throw new \Exception("La traduction pour la permission '{$name}' est introuvable.");
    //             }

    //             // Mettre à jour les informations du permission
    //             $permission->update([
    //                 'name' => $name,
    //                 'guard_name' => $guardName, // Correspondance ou valeur par défaut
    //                 'translation' => $translation, // Traduction ou valeur par défaut
    //             ]);


    //             return redirect()->route('admin.permissions.index')
    //             ->with('success', 'Permission mise à jour avec succès');

    // }
    public function update(Request $request, Permission $permission)
    {
        // Valider les données de la requête
        $validatedData = $request->validate([
            'action' => 'required|string|in:create,view,edit,delete',
            'resource' => 'required|string|in:' . implode(',', Permission::getResources()),
        ]);

        // Construire le champ `name` en combinant `action` et `resource`
        $name = strtolower($validatedData['action'] . '-' . $validatedData['resource']);

        // Vérifier l'unicité du champ `name`, en excluant l'ID actuel
        $request->validate([
            'name' => 'string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        // Obtenir les correspondances `guard_name` et `translation`
        $guardMapping = $this->getPermissionMapping();
        $guardName = $guardMapping[$name]['guard'] ?? $permission->guard_name; // Utiliser l'ancien guard_name si non trouvé
        $translation = $guardMapping[$name]['translation'] ?? ucfirst($name); // Utiliser l'ancien translation si non trouvé

        // Mettre à jour les informations de la permission
        $permission->update([
            'name' => $name,
            'guard_name' => $guardName,
            'translation' => $translation,
        ]);

        // Redirection avec un message de succès
        return redirect()->route('admin.permissions.index')->with('success', 'Permission modifiée avec succès.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
           // Supprimer la permission
        $permission->delete();

        // Réinitialiser le cache des permissions
        Artisan::call('permission:cache-reset');


        return redirect()->route('admin.permissions.index')->with('success', 'Permission supprimée avec succès');
    }


    private function getPermissionMapping(): array
    {
        // Récupérer les permissions depuis la base de données
        $permissions = \DB::table('permissions')->get(['name', 'guard_name']);

        // Si des permissions existent dans la base de données, les mapper dynamiquement
        if ($permissions->isNotEmpty()) {
            return $permissions->mapWithKeys(function ($permission) {
                return [
                    $permission->name => [
                        'guard' => $permission->guard_name,
                        'translation' => ucfirst($permission->name), // Par défaut : capitalisation du nom
                    ],
                ];
            })->toArray();
        }

        // Si la table des rôles est vide, retourner le tableau par défaut

        return [
            // Utilisateurs
            'create-users' => ['guard' => 'web', 'translation' => 'Créer des utilisateurs'],
            'view-users' => ['guard' => 'web', 'translation' => 'Voir les utilisateurs'],
            'edit-users' => ['guard' => 'web', 'translation' => 'Modifier des utilisateurs'],
            'delete-users' => ['guard' => 'web', 'translation' => 'Supprimer des utilisateurs'],

            // Rôles
            'create-roles' => ['guard' => 'web', 'translation' => 'Créer des rôles'],
            'view-roles' => ['guard' => 'web', 'translation' => 'Voir les rôles'],
            'edit-roles' => ['guard' => 'web', 'translation' => 'Modifier des rôles'],
            'delete-roles' => ['guard' => 'web', 'translation' => 'Supprimer des rôles'],

            // Permissions
            'create-permissions' => ['guard' => 'web', 'translation' => 'Créer des permissions'],
            'view-permissions' => ['guard' => 'web', 'translation' => 'Voir les permissions'],
            'edit-permissions' => ['guard' => 'web', 'translation' => 'Modifier des permissions'],
            'delete-permissions' => ['guard' => 'web', 'translation' => 'Supprimer des permissions'],

            // Coupons
            'create-coupons' => ['guard' => 'web', 'translation' => 'Créer des coupons'],
            'view-coupons' => ['guard' => 'web', 'translation' => 'Voir les coupons'],
            'edit-coupons' => ['guard' => 'web', 'translation' => 'Modifier des coupons'],
            'delete-coupons' => ['guard' => 'web', 'translation' => 'Supprimer des coupons'],

            // Produits
            'create-products' => ['guard' => 'web', 'translation' => 'Créer des produits'],
            'view-products' => ['guard' => 'web', 'translation' => 'Voir les produits'],
            'edit-products' => ['guard' => 'web', 'translation' => 'Modifier des produits'],
            'delete-products' => ['guard' => 'web', 'translation' => 'Supprimer des produits'],

            // Commandes
            'create-orders' => ['guard' => 'web', 'translation' => 'Créer des commandes'],
            'view-orders' => ['guard' => 'web', 'translation' => 'Voir les commandes'],
            'edit-orders' => ['guard' => 'web', 'translation' => 'Modifier des commandes'],
            'delete-orders' => ['guard' => 'web', 'translation' => 'Supprimer des commandes'],

            // Blogs
            'create-blogs' => ['guard' => 'web', 'translation' => 'Créer des articles de blog'],
            'view-blogs' => ['guard' => 'web', 'translation' => 'Voir les articles de blog'],
            'edit-blogs' => ['guard' => 'web', 'translation' => 'Modifier des articles de blog'],
            'delete-blogs' => ['guard' => 'web', 'translation' => 'Supprimer des articles de blog'],

            // Menus
            'create-menus' => ['guard' => 'web', 'translation' => 'Créer des menus'],
            'view-menus' => ['guard' => 'web', 'translation' => 'Voir les menus'],
            'edit-menus' => ['guard' => 'web', 'translation' => 'Modifier des menus'],
            'delete-menus' => ['guard' => 'web', 'translation' => 'Supprimer des menus'],

            // Tableau de bord
            'access-dashboard' => ['guard' => 'web', 'translation' => 'Accéder au tableau de bord'],

            // Paramètres
            'manage-settings' => ['guard' => 'web', 'translation' => 'Gérer les paramètres'],

            // Rapports
            'view-reports' => ['guard' => 'web', 'translation' => 'Voir les rapports'],
            'export-reports' => ['guard' => 'web', 'translation' => 'Exporter les rapports'],
        ];
    }


}

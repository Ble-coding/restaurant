<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Réinitialiser la table des utilisateurs et leurs relations
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('users')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        // // Définition des rôles
        // $roles = [
        //     'super_admin',
        //     'admin',
        //     'manager',
        //     'editor',
        //     'viewer',
        // ];

        // // Création des utilisateurs
        // $users = [];
        // foreach (range(1, 15) as $index) {
        //     $role = $roles[array_rand($roles)]; // Rôle aléatoire pour chaque utilisateur
        //     $users[] = [
        //         'name' => "User $index",
        //         'email' => "user$index@example.com",
        //         'password' => bcrypt('12345678'),
        //         'country_code' => '225',
        //         'phone' => '555' . str_pad($index, 4, '0', STR_PAD_LEFT),
        //         'role' => $role,
        //     ];
        // }

        // // Création des utilisateurs en base de données
        // foreach ($users as $userData) {
        //     $user = User::create([
        //         'name' => $userData['name'],
        //         'email' => $userData['email'],
        //         'password' => $userData['password'],
        //         'country_code' => $userData['country_code'],
        //         'phone' => $userData['phone'],
        //     ]);

        //     // Assigner le rôle
        //     $role = Role::firstOrCreate(['name' => $userData['role']], ['guard_name' => 'web']);
        //     $user->assignRole($role);

        //     // Si c'est un super_admin, lui attribuer toutes les permissions
        //     if ($userData['role'] === 'super_admin') {
        //         $user->givePermissionTo(Permission::all());
        //     } else {
        //         // Assigner des permissions spécifiques selon le rôle
        //         $this->assignPermissionsBasedOnRole($user, $role);
        //     }
        // }
    }

    // /**
    //  * Assigner des permissions spécifiques en fonction du rôle
    //  */
    // private function assignPermissionsBasedOnRole(User $user, Role $role): void
    // {
    //     // Obtenir toutes les permissions disponibles
    //     $permissionMapping = $this->getPermissionMapping();
    //     $permissions = array_keys($permissionMapping); // Récupérer les clés (permissions)

    //     // Initialiser les permissions spécifiques pour chaque rôle
    //     $rolePermissions = [];

    //     switch ($role->name) {
    //         case 'admin':
    //             // L'admin peut gérer utilisateurs, rôles, permissions, catégories et accéder au tableau de bord
    //             $rolePermissions = [
    //                 'access-dashboard',
    //                 'create-users', 'view-users', 'edit-users', 'delete-users',
    //                 'create-roles', 'view-roles', 'edit-roles', 'delete-roles',
    //                 'create-categories', 'view-categories', 'edit-categories', 'delete-categories',
    //             ];
    //             break;

    //         case 'manager':
    //             // Le manager peut gérer commandes, utilisateurs en lecture seule, et voir le tableau de bord
    //             $rolePermissions = [
    //                 'access-dashboard',
    //                 'view-users',
    //                 'create-orders', 'view-orders', 'edit-orders', 'delete-orders',
    //             ];
    //             break;

    //         case 'editor':
    //             // L'éditeur peut gérer blogs, produits, et menus
    //             $rolePermissions = [
    //                 'create-blogs', 'view-blogs', 'edit-blogs', 'delete-blogs',
    //                 'create-products', 'view-products', 'edit-products', 'delete-products',
    //                 'create-menus', 'view-menus', 'edit-menus', 'delete-menus',
    //             ];
    //             break;

    //         case 'viewer':
    //             // Le viewer a uniquement des permissions en lecture
    //             $rolePermissions = array_filter($permissions, fn($perm) => str_starts_with($perm, 'view-'));
    //             break;

    //         default:
    //             // Aucun rôle spécifique, pas de permissions
    //             $rolePermissions = [];
    //             break;
    //     }

    //     // Assigner les permissions au rôle et à l'utilisateur
    //     $role->syncPermissions($rolePermissions);
    //     $user->givePermissionTo($rolePermissions);
    // }


    // private function getPermissionMapping(): array
    // {
    //     return [
    //         // Section Accueil
    //         'access-dashboard' => ['guard' => 'web', 'translation' => 'Accéder au tableau de bord'],

    //         // Section Utilisateurs
    //         'create-users' => ['guard' => 'web', 'translation' => 'Créer des utilisateurs'],
    //         'view-users' => ['guard' => 'web', 'translation' => 'Voir les utilisateurs'],
    //         'edit-users' => ['guard' => 'web', 'translation' => 'Modifier des utilisateurs'],
    //         'delete-users' => ['guard' => 'web', 'translation' => 'Supprimer des utilisateurs'],

    //         // Section Rôles
    //         'create-roles' => ['guard' => 'web', 'translation' => 'Créer des rôles'],
    //         'view-roles' => ['guard' => 'web', 'translation' => 'Voir les rôles'],
    //         'edit-roles' => ['guard' => 'web', 'translation' => 'Modifier des rôles'],
    //         'delete-roles' => ['guard' => 'web', 'translation' => 'Supprimer des rôles'],

    //         // Section Permissions
    //         'create-permissions' => ['guard' => 'web', 'translation' => 'Créer des permissions'],
    //         'view-permissions' => ['guard' => 'web', 'translation' => 'Voir les permissions'],
    //         'edit-permissions' => ['guard' => 'web', 'translation' => 'Modifier des permissions'],
    //         'delete-permissions' => ['guard' => 'web', 'translation' => 'Supprimer des permissions'],

    //         // Section Menus
    //         'create-menus' => ['guard' => 'web', 'translation' => 'Créer des menus'],
    //         'view-menus' => ['guard' => 'web', 'translation' => 'Voir les menus'],
    //         'edit-menus' => ['guard' => 'web', 'translation' => 'Modifier des menus'],
    //         'delete-menus' => ['guard' => 'web', 'translation' => 'Supprimer des menus'],

    //         // Section Catégories
    //         'create-categories' => ['guard' => 'web', 'translation' => 'Créer des catégories'],
    //         'view-categories' => ['guard' => 'web', 'translation' => 'Voir les catégories'],
    //         'edit-categories' => ['guard' => 'web', 'translation' => 'Modifier des catégories'],
    //         'delete-categories' => ['guard' => 'web', 'translation' => 'Supprimer des catégories'],

    //         // Section Commandes
    //         'create-orders' => ['guard' => 'web', 'translation' => 'Créer des commandes'],
    //         'view-orders' => ['guard' => 'web', 'translation' => 'Voir les commandes'],
    //         'edit-orders' => ['guard' => 'web', 'translation' => 'Modifier des commandes'],
    //         'delete-orders' => ['guard' => 'web', 'translation' => 'Supprimer des commandes'],

    //         // Section Blogs
    //         'create-blogs' => ['guard' => 'web', 'translation' => 'Créer des blogs'],
    //         'view-blogs' => ['guard' => 'web', 'translation' => 'Voir les blogs'],
    //         'edit-blogs' => ['guard' => 'web', 'translation' => 'Modifier des blogs'],
    //         'delete-blogs' => ['guard' => 'web', 'translation' => 'Supprimer des blogs'],

    //         // Section Coupons
    //         'create-coupons' => ['guard' => 'web', 'translation' => 'Créer des coupons'],
    //         'view-coupons' => ['guard' => 'web', 'translation' => 'Voir les coupons'],
    //         'edit-coupons' => ['guard' => 'web', 'translation' => 'Modifier des coupons'],
    //         'delete-coupons' => ['guard' => 'web', 'translation' => 'Supprimer des coupons'],

    //         // Section Articles
    //         'create-articles' => ['guard' => 'web', 'translation' => 'Créer des articles'],
    //         'view-articles' => ['guard' => 'web', 'translation' => 'Voir les articles'],
    //         'edit-articles' => ['guard' => 'web', 'translation' => 'Modifier des articles'],
    //         'delete-articles' => ['guard' => 'web', 'translation' => 'Supprimer des articles'],

    //         // Section Produits
    //         'create-products' => ['guard' => 'web', 'translation' => 'Créer des produits'],
    //         'view-products' => ['guard' => 'web', 'translation' => 'Voir les produits'],
    //         'edit-products' => ['guard' => 'web', 'translation' => 'Modifier des produits'],
    //         'delete-products' => ['guard' => 'web', 'translation' => 'Supprimer des produits'],
    //     ];
    // }

}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

            // Réinitialiser la table des permissions
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');


            $permissions = $this->getPermissionMapping();

            foreach ($permissions as $name => $attributes) {
                Permission::create([
                    'name' => $name,
                    'guard_name' => $attributes['guard'],
                    'translation' => $attributes['translation'],
                ]);
            }
    }

    private function getPermissionMapping(): array
    {
        return [
            // Section Accueil
            'access-dashboard' => ['guard' => 'web', 'translation' => 'Accéder au tableau de bord'],

            // Section Utilisateurs
            'create-users' => ['guard' => 'web', 'translation' => 'Créer des utilisateurs'],
            'view-users' => ['guard' => 'web', 'translation' => 'Voir les utilisateurs'],
            'edit-users' => ['guard' => 'web', 'translation' => 'Modifier des utilisateurs'],
            'delete-users' => ['guard' => 'web', 'translation' => 'Supprimer des utilisateurs'],

            // Section Rôles
            'create-roles' => ['guard' => 'web', 'translation' => 'Créer des rôles'],
            'view-roles' => ['guard' => 'web', 'translation' => 'Voir les rôles'],
            'edit-roles' => ['guard' => 'web', 'translation' => 'Modifier des rôles'],
            'delete-roles' => ['guard' => 'web', 'translation' => 'Supprimer des rôles'],

            // Section Permissions
            'create-permissions' => ['guard' => 'web', 'translation' => 'Créer des permissions'],
            'view-permissions' => ['guard' => 'web', 'translation' => 'Voir les permissions'],
            'edit-permissions' => ['guard' => 'web', 'translation' => 'Modifier des permissions'],
            'delete-permissions' => ['guard' => 'web', 'translation' => 'Supprimer des permissions'],

            // Section Menus
            'create-menus' => ['guard' => 'web', 'translation' => 'Créer des menus'],
            'view-menus' => ['guard' => 'web', 'translation' => 'Voir les menus'],
            'edit-menus' => ['guard' => 'web', 'translation' => 'Modifier des menus'],
            'delete-menus' => ['guard' => 'web', 'translation' => 'Supprimer des menus'],

            // Section Catégories
            'create-categories' => ['guard' => 'web', 'translation' => 'Créer des catégories'],
            'view-categories' => ['guard' => 'web', 'translation' => 'Voir les catégories'],
            'edit-categories' => ['guard' => 'web', 'translation' => 'Modifier des catégories'],
            'delete-categories' => ['guard' => 'web', 'translation' => 'Supprimer des catégories'],

            // Section Commandes
            'create-orders' => ['guard' => 'web', 'translation' => 'Créer des commandes'],
            'view-orders' => ['guard' => 'web', 'translation' => 'Voir les commandes'],
            'edit-orders' => ['guard' => 'web', 'translation' => 'Modifier des commandes'],
            'delete-orders' => ['guard' => 'web', 'translation' => 'Supprimer des commandes'],

            // Section Blogs
            'create-blogs' => ['guard' => 'web', 'translation' => 'Créer des blogs'],
            'view-blogs' => ['guard' => 'web', 'translation' => 'Voir les blogs'],
            'edit-blogs' => ['guard' => 'web', 'translation' => 'Modifier des blogs'],
            'delete-blogs' => ['guard' => 'web', 'translation' => 'Supprimer des blogs'],

            // Section Coupons
            'create-coupons' => ['guard' => 'web', 'translation' => 'Créer des coupons'],
            'view-coupons' => ['guard' => 'web', 'translation' => 'Voir les coupons'],
            'edit-coupons' => ['guard' => 'web', 'translation' => 'Modifier des coupons'],
            'delete-coupons' => ['guard' => 'web', 'translation' => 'Supprimer des coupons'],

            // Section Articles
            'create-articles' => ['guard' => 'web', 'translation' => 'Créer des articles'],
            'view-articles' => ['guard' => 'web', 'translation' => 'Voir les articles'],
            'edit-articles' => ['guard' => 'web', 'translation' => 'Modifier des articles'],
            'delete-articles' => ['guard' => 'web', 'translation' => 'Supprimer des articles'],

            // Section Produits
            'create-products' => ['guard' => 'web', 'translation' => 'Créer des produits'],
            'view-products' => ['guard' => 'web', 'translation' => 'Voir les produits'],
            'edit-products' => ['guard' => 'web', 'translation' => 'Modifier des produits'],
            'delete-products' => ['guard' => 'web', 'translation' => 'Supprimer des produits'],
        ];
    }

}

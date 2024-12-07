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
            // Supprimer toutes les permissions existantes
            DB::table('permissions')->delete();

            $permissions = $this->getPermissionMapping();

            foreach ($permissions as $name => $attributes) {
                Permission::create([
                    'name' => $name,
                    'guard_name' => $attributes['guard'],
                    'translation' => $attributes['translation'],
                ]);
            }
        // $permissions = [
        //     'view_users',
        //     'create_users',
        //     'edit_users',
        //     'delete_users',
        //     'view_products',
        //     'create_products',
        //     'edit_products',
        //     'delete_products',
        //     'manage_orders',
        //     'view_reports'
        // ];

        // foreach ($permissions as $permission) {
        //     Permission::create(['name' => $permission, 'guard_name' => 'web']);
        // }
    }

    private function getPermissionMapping(): array
    {
        return [
            'create-users' => ['guard' => 'web', 'translation' => 'Créer des utilisateurs'],
            'read-users' => ['guard' => 'web', 'translation' => 'Voir les utilisateurs'],
            'edit-users' => ['guard' => 'web', 'translation' => 'Modifier des utilisateurs'],
            'delete-users' => ['guard' => 'web', 'translation' => 'Supprimer des utilisateurs'],

            'create-roles' => ['guard' => 'web', 'translation' => 'Créer des rôles'],
            'read-roles' => ['guard' => 'web', 'translation' => 'Voir les rôles'],
            'edit-roles' => ['guard' => 'web', 'translation' => 'Modifier des rôles'],
            'delete-roles' => ['guard' => 'web', 'translation' => 'Supprimer des rôles'],

            'create-permissions' => ['guard' => 'web', 'translation' => 'Créer des permissions'],
            'read-permissions' => ['guard' => 'web', 'translation' => 'Voir les permissions'],
            'edit-permissions' => ['guard' => 'web', 'translation' => 'Modifier des permissions'],
            'delete-permissions' => ['guard' => 'web', 'translation' => 'Supprimer des permissions'],

            'create-products' => ['guard' => 'web', 'translation' => 'Créer des produits'],
            'read-products' => ['guard' => 'web', 'translation' => 'Voir les produits'],
            'edit-products' => ['guard' => 'web', 'translation' => 'Modifier des produits'],
            'delete-products' => ['guard' => 'web', 'translation' => 'Supprimer des produits'],

            'access-dashboard' => ['guard' => 'web', 'translation' => 'Accéder au tableau de bord'],
        ];
    }
}

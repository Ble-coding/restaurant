<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Vider les tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $resources = ['Utilisateurs', 'Menus', 'Catégorie', 'Commandes', 'Blogs',
        'Rôles', 'Permissions', 'Coupons','Articles', 'Produits'];
        $actions = ['create', 'edit', 'delete', 'view'];

        // 3. Créer les permissions
        $permissions = [];
        foreach ($resources as $resource) {
            $resourceSlug = strtolower($resource);
            foreach ($actions as $action) {
                $permissions[] = [
                    'name' => "{$action}-{$resourceSlug}",
                    'guard_name' => 'web',
                    'translation' => ucfirst($action) . " " . strtolower($resource),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        // Insérer les permissions
        Permission::insert($permissions);
    }
}

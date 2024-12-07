<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Obtenir ou créer l'utilisateur
         $admin = User::where('email', 'admin@admin.com')->first();
         if (!$admin) {
             $admin = User::create([
                 'name' => 'Admin',
                 'email' => 'admin@admin.com',
                 'password' => bcrypt('password'), // Changez le mot de passe pour plus de sécurité
             ]);
         }

         // Obtenir ou créer le rôle "Admin"
         $role = Role::firstOrCreate(['name' => 'Admin']);

         // Assigner toutes les permissions au rôle "Admin"
         $permissions = Permission::all();
         $role->syncPermissions($permissions);

         // Assigner le rôle "Admin" à l'utilisateur
         $admin->assignRole($role);
    }
}

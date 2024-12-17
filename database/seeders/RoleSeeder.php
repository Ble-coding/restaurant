<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {

        // Réinitialiser la table des rôles
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');



        $roles = [
            ['name' => 'super_admin', 'translation' => 'Super Administrateur'],
            ['name' => 'admin', 'translation' => 'Administrateur'],
            ['name' => 'manager', 'translation' => 'Manager'],
            ['name' => 'editor', 'translation' => 'Éditeur'],
            ['name' => 'viewer', 'translation' => 'Visiteur'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], [
                'translation' => $role['translation'],
                'guard_name' => 'web',
            ]);
        }
    }
}

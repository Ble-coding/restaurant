<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin' => 'Administrateur',
            'editor' => 'Éditeur',
            'user' => 'Utilisateur',
            'moderator' => 'Modérateur',
            'manager' => 'Gestionnaire',
            'customer' => 'Client',
            'seller' => 'Vendeur',
            'support' => 'Support technique',
            'developer' => 'Développeur',
            'guest' => 'Invité',
        ];
    }
}

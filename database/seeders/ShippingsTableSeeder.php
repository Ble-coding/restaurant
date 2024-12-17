<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shipping;
use Illuminate\Support\Facades\DB;


class ShippingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shipping::insert([
            ['name' => 'Gratuit', 'price' => 0],
            ['name' => 'Standard', 'price' => 5.00], // Exemple de prix standard
        ]);
    }
}

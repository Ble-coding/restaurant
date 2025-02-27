<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;


class ProductNameAndDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $translations = [
            [
                "name" => ["en" => "Fresh Orange Juice", "fr" => "Jus d'orange frais"],
                "description" => [
                    "en" => "A refreshing glass of fresh orange juice made from organic oranges.",
                    "fr" => "Un verre rafraîchissant de jus d'orange frais fait à partir d'oranges biologiques."
                ],
            ],
            [
                "name" => ["en" => "Iced Lemon Tea", "fr" => "Thé glacé au citron"],
                "description" => [
                    "en" => "A cold and tangy iced tea with a hint of lemon.",
                    "fr" => "Un thé glacé froid et acidulé avec une touche de citron."
                ],
            ],
            [
                "name" => ["en" => "Classic Cola", "fr" => "Cola classique"],
                "description" => [
                    "en" => "The classic cola drink loved by all.",
                    "fr" => "La boisson cola classique appréciée de tous."
                ],
            ],
            [
                "name" => ["en" => "Sparkling Water", "fr" => "Eau pétillante"],
                "description" => [
                    "en" => "Pure sparkling water to keep you hydrated.",
                    "fr" => "De l'eau pétillante pure pour vous hydrater."
                ],
            ],
            [
                "name" => ["en" => "Mango Smoothie", "fr" => "Smoothie à la mangue"],
                "description" => [
                    "en" => "A creamy mango smoothie made with fresh mangoes.",
                    "fr" => "Un smoothie crémeux à la mangue fait avec des mangues fraîches."
                ],
            ],
            [
                "name" => ["en" => "Green Tea", "fr" => "Thé vert"],
                "description" => [
                    "en" => "A warm cup of green tea full of antioxidants.",
                    "fr" => "Une tasse chaude de thé vert riche en antioxydants."
                ],
            ],
            [
                "name" => ["en" => "Hot Chocolate", "fr" => "Chocolat chaud"],
                "description" => [
                    "en" => "Rich and creamy hot chocolate for cold days.",
                    "fr" => "Un chocolat chaud riche et crémeux pour les jours froids."
                ],
            ],
            [
                "name" => ["en" => "Strawberry Milkshake", "fr" => "Milkshake à la fraise"],
                "description" => [
                    "en" => "A sweet and fruity milkshake made with fresh strawberries.",
                    "fr" => "Un milkshake sucré et fruité fait avec des fraises fraîches."
                ],
            ],
            [
                "name" => ["en" => "Espresso", "fr" => "Espresso"],
                "description" => [
                    "en" => "A strong and bold espresso shot for coffee lovers.",
                    "fr" => "Un espresso fort et audacieux pour les amateurs de café."
                ],
            ],
            [
                "name" => ["en" => "Lemonade", "fr" => "Limonade"],
                "description" => [
                    "en" => "A cool and refreshing lemonade made with fresh lemons.",
                    "fr" => "Une limonade fraîche et rafraîchissante faite avec des citrons frais."
                ],
            ],
        ];

        // Récupérer tous les produits existants
        $products = Product::all();

        // Parcourir les produits existants et ajouter les traductions
        $products->each(function ($product, $index) use ($translations) {
            // Récupérer les données de traduction par index
            $translation = $translations[$index % count($translations)];

            // Mettre à jour les champs `name` et `description` en JSON
            $product->update([
                'name' => $translation['name'],
                'description' => $translation['description'],
            ]);
        });

        $this->command->info('Produits mis à jour avec noms et descriptions traduits.');

    }
}

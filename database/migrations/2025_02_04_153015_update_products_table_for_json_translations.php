<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Suppression des colonnes actuelles
            $table->dropColumn(['name', 'description', 'status']);

            // Ajout des nouvelles colonnes pour les traductions
            $table->json('name')->nullable();
            $table->json('description')->nullable();
            $table->json('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Restauration des colonnes d'origine
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('available');

            // Suppression des colonnes JSON
            $table->dropColumn(['name', 'description', 'status']);
        });
    }
};

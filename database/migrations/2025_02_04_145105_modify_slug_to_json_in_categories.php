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
        Schema::table('categories', function (Blueprint $table) {
            // Supprimer la colonne `slug` existante (type string)
            $table->dropColumn('slug');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Ajouter la nouvelle colonne `slug` en JSON
            $table->json('slug')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Supprimer la colonne `slug` en JSON
            $table->dropColumn('slug');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Restaurer `slug` en string
            $table->string('slug')->unique()->after('name');
        });
    }
};

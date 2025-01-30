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
        Schema::table('roles', function (Blueprint $table) {
            // Supprimer la colonne `name` de type JSON
            $table->dropColumn('name');

            // Ajouter les colonnes `name_fr` et `name_en`
            $table->string('name_fr')->nullable()->after('guard_name');
            $table->string('name_en')->nullable()->after('name_fr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Restaurer la colonne `name` de type JSON
            $table->json('name')->nullable()->after('guard_name');

            // Supprimer les colonnes `name_fr` et `name_en`
            $table->dropColumn('name_fr');
            $table->dropColumn('name_en');
        });
    }
};

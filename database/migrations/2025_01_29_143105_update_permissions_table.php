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
        Schema::table('permissions', function (Blueprint $table) {
             // Ajouter les nouvelles colonnes pour les noms traduits
             $table->string('name_fr')->nullable()->after('name');
             $table->string('name_en')->nullable()->after('name_fr');

             // Renommer la colonne `translation` en `translation_fr`
             $table->renameColumn('translation', 'translation_fr');

             // Ajouter une nouvelle colonne pour `translation_en`
             $table->text('translation_en')->nullable()->after('translation_fr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
                // Supprimer les nouvelles colonnes
                $table->dropColumn(['name_fr', 'name_en', 'translation_en']);

                // Renommer `translation_fr` en `translation` (revenir à l'état initial)
                $table->renameColumn('translation_fr', 'translation');
        });
    }
};

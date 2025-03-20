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
        // Schema::table('orders', function (Blueprint $table) {
        //       // Supprime l'ancien champ status s'il est de type string
        //       if (Schema::hasColumn('orders', 'status')) {
        //         $table->dropColumn('status');
        //     }

        //     // Ajoute le nouveau champ status en JSON pour Spatie Translatable
        //     $table->json('status')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('orders', function (Blueprint $table) {
        //          // Supprime le champ JSON et remet un champ string par défaut
        //          $table->dropColumn('status');
        //          $table->string('status')->default('pending');
        // });
    }
};

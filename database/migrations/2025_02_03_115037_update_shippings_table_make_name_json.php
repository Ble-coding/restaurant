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
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn('name'); // Supprimer l'ancienne colonne name en string
        });

        Schema::table('shippings', function (Blueprint $table) {
            $table->json('name')->nullable(); // Ajouter la nouvelle colonne name en JSON
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn('name'); // Supprimer la colonne JSON si on fait un rollback
        });

        Schema::table('shippings', function (Blueprint $table) {
            $table->string('name')->nullable(); // Réintroduire name en string en cas de rollback
        });
    }
};

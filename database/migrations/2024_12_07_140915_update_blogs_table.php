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
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn('category');

            // Ajouter `category_id` comme clé étrangère
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');

            // Ajouter une nouvelle colonne `status`
            $table->string('status')->default('draft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
             // Restaurer l'ancienne colonne `category`
             $table->string('category')->nullable();

             // Supprimer les nouvelles colonnes
             $table->dropForeign(['category_id']);
             $table->dropColumn('category_id');
             $table->dropColumn('status');
        });
    }
};

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
        Schema::table('translation_settings', function (Blueprint $table) {
              // Supprimer la colonne type_id et sa clé étrangère
              $table->dropForeign(['type_id']);
              $table->dropColumn('type_id');

              // Ajouter la colonne service_id et définir une clé étrangère
              $table->unsignedBigInteger('service_id')->nullable()->after('user_id');
              $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_settings', function (Blueprint $table) {
             // Supprimer la colonne service_id et sa clé étrangère
             $table->dropForeign(['service_id']);
             $table->dropColumn('service_id');

             // Restaurer la colonne type_id et définir une clé étrangère
             $table->unsignedBigInteger('type_id')->nullable()->after('user_id');
             $table->foreign('type_id')->references('id')->on('services')->onDelete('set null');
        });
    }
};

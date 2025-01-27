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
             // Ajouter la colonne type_id et la clé étrangère
             $table->unsignedBigInteger('type_id')->nullable()->after('user_id');
             $table->foreign('type_id')->references('id')->on('services')->onDelete('set null');

             // Supprimer la colonne type
             $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_settings', function (Blueprint $table) {
              // Restaurer la colonne type
              $table->string('type')->default('deepl')->after('user_id');

              // Supprimer la colonne type_id et la clé étrangère
              $table->dropForeign(['type_id']);
              $table->dropColumn('type_id');
        });
    }
};

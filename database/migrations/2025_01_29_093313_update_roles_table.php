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
            // Suppression de la colonne actuelle `name`
            // $table->dropColumn('name');
            // $table->dropColumn('translation');

            // Ajout de la colonne JSON pour la traduction du nom
            $table->json('name')->nullable();

            // $table->json('name')->nullable()->change();

            // Ajout de l'identifiant utilisateur
            $table->unsignedBigInteger('user_id')->nullable();

            // Ajout d'une colonne pour la gestion des traductions
            $table->string('slug')->nullable();

            // Si besoin, ajout de contraintes pour `user_id`
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Restauration des modifications
            $table->dropForeign(['user_id']);
            $table->dropColumn(['name', 'user_id', 'slug']);
            $table->string('name');
            $table->string('translation');
        });
    }
};

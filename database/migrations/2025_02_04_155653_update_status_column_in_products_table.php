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
              // Suppression de l'ancienne colonne JSON
              $table->dropColumn('status');

              // Ajout de la nouvelle colonne string avec une valeur par défaut
              $table->string('status')->default('available')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->json('status')->nullable()->after('description');
        });
    }
};

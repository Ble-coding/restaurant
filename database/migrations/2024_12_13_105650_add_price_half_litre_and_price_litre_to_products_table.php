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
            $table->decimal('price_half_litre', 8, 2)->nullable()->after('price'); // Ajouter après la colonne 'price'
            $table->decimal('price_litre', 8, 2)->nullable()->after('price_half_litre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Suppression des colonnes 'price_half_litre' et 'price_litre'
            $table->dropColumn(['price_half_litre', 'price_litre']);
        });
    }
};

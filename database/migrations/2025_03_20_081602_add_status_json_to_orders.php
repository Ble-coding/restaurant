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
        Schema::table('orders', function (Blueprint $table) {
            $table->json('status')->nullable()->after('total');
        });

        // Mise à jour des anciennes commandes pour avoir une valeur JSON par défaut
        DB::table('orders')->update([
            'status' => json_encode([
                'en' => 'pending',
                'fr' => 'En attente',
            ])
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

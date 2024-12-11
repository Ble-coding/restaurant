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
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('name'); // Supprime la colonne 'name'
            $table->string('lastname')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('lastname'); // Supprime la colonne 'last_name'
            // $table->string('name')->after('id'); // Réajoute la colonne 'name'
        });
    }
};

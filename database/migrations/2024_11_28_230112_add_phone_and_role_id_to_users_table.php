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
        Schema::table('users', function (Blueprint $table) {
            // $table->string('phone')->nullable()->unique();
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);

            // Supprimer la colonne 'role_id'
            $table->dropColumn('role_id');

            // Supprimer l'unicité et la colonne 'phone'
            // $table->dropUnique(['phone']);
            // $table->dropColumn('phone');
        });
    }
};

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
            $table->unique(['api_key', 'service_id'], 'unique_api_key_service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_settings', function (Blueprint $table) {
            $table->dropUnique('unique_api_key_service_id');
        });
    }
};

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
            $table->string('title_fr')->after('id');
            $table->string('title_en')->after('title_fr');
            $table->text('content_fr')->nullable()->after('title_en');
            $table->text('content_en')->nullable()->after('content_fr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['title_fr', 'title_en', 'content_fr', 'content_en']);
        });
    }
};

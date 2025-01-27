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
        Schema::create('translation_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Associez la clé à un utilisateur spécifique si nécessaire
            $table->string('type')->default('deepl');
            $table->string('api_key'); // Clé API DeepL
            $table->string('source_lang')->default('FR'); // Langue source par défaut
            $table->string('target_lang')->default('EN'); // Langue cible par défaut
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_settings');
    }
};

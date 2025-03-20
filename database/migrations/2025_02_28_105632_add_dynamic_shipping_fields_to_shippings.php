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
        Schema::table('shippings', function (Blueprint $table) {
            $table->enum('type', ['free', 'paid', 'conditional'])->default('paid')->after('name');
            $table->decimal('min_price_for_free', 8, 2)->nullable()->after('price');
            $table->json('conditions')->nullable()->after('min_price_for_free');  //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn(['type', 'min_price_for_free', 'conditions']);
        });
    }
};

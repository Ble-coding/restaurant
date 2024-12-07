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
             // Modifier les champs existants
             $table->renameColumn('customer_name', 'first_name');
             $table->string('last_name')->after('first_name');
             $table->renameColumn('customer_email', 'email');
             $table->string('phone')->after('email');
             $table->string('address')->after('phone');
             $table->string('city')->after('address');
             $table->string('zip')->after('city');

             // Ajout de nouvelles colonnes
             $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
             $table->text('order_notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('first_name', 'customer_name');
            $table->dropColumn(['last_name', 'phone', 'address', 'city', 'zip', 'coupon_id', 'order_notes']);
            $table->renameColumn('email', 'customer_email');
        });
    }
};

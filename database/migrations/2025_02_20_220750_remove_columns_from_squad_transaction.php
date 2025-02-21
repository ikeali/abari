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
        Schema::table('squad_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'event', 
                'gateway_ref', 
                'merchant_amount', 
                'email', 
                'transaction_type', 
                'customer_mobile', 
                'meta', 
                'payment_information', 
                'transaction_date'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('squad_transactions', function (Blueprint $table) {
            //
        });
    }
};

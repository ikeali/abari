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
        Schema::create('squad_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('event')->default('charge_successful');
            $table->string('transaction_reference')->unique();
            $table->string('gateway_ref')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('merchant_amount', 15, 2)->nullable();
            $table->string('currency', 10)->default('NGN');
            $table->string('email')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('customer_mobile')->nullable();
            $table->json('meta')->nullable(); // Stores metadata as JSON
            $table->json('payment_information')->nullable(); // Stores card/payment info as JSON
            $table->timestamp('transaction_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('squad_transactions');
    }
};

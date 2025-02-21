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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();
            $table->string('virtual_account_number');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('settled_amount', 15, 2);
            $table->decimal('fee_charged', 15, 2);
            $table->timestamp('transaction_date');
            $table->string('customer_identifier');
            $table->string('transaction_indicator');
            $table->string('currency')->default('NGN');
            $table->string('channel');
            $table->string('sender_name')->nullable();
            $table->text('remarks')->nullable();
            $table->json('meta')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('gateway_ref')->nullable();
            $table->string('email')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('transaction_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

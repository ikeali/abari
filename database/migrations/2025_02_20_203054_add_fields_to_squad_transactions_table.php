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
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank')->nullable();
            $table->decimal('expected_amount', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->boolean('is_blocked')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('squad_transactions', function (Blueprint $table) {
            $table->dropColumn(['account_name', 'account_number', 'bank', 'expected_amount', 'is_blocked', 'amount']);

        });
    }
};

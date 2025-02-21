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
            $table->boolean('refund')->nullable()->after('transaction_status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('squad_transactions', function (Blueprint $table) {
            $table->dropColumn('refund');

        });
    }
};

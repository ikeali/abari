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
            $table->renameColumn('transaction_reference', 'transaction_ref');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('squad_transactions', function (Blueprint $table) {
            $table->renameColumn('transaction_ref', 'transaction_reference');

        });
    }
};

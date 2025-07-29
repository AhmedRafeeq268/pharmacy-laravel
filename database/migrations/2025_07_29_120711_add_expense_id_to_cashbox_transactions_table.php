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
        Schema::table('cashbox_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_id')->nullable()->after('session_id');

            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashbox_transactions', function (Blueprint $table) {
            //
        });
    }
};

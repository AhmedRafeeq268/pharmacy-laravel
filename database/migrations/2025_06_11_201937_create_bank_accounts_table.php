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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('IPAN');
            $table->integer('bank_cd');
            $table->bigInteger('wallet_phone_number');
            $table->integer('wallet_cd');
            $table->integer('currency_cd');
            $table->integer('status_cd');
            $table->unsignedBigInteger('accountable_id');    // ID الموظف أو المورد
            $table->integer('accountable_type_cd');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};

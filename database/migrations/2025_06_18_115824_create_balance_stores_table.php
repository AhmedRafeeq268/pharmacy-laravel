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
        Schema::create('balance_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->text('product_name');
            $table->date('production_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->integer('quantity');
            $table->text('manufacture')->nullable();
            $table->integer('unity_price');
            $table->integer('remaining_quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_stores');
    }
};

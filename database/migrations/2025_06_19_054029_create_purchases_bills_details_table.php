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
        Schema::create('purchases_bills_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->string('product_name');
            $table->unsignedBigInteger('product_id');
            $table->string('product_category');
            $table->string('product_data');
            $table->integer('quantity');
            $table->integer('cost');
            $table->integer('total');
            $table->integer('discount');
            $table->unsignedBigInteger('employee_id');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_bills_details');
    }
};

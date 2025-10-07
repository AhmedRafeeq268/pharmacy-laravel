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
        Schema::create('pos_bill_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_bill_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('unit_price');
            $table->integer('quantity');
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_bill_details');
    }
};

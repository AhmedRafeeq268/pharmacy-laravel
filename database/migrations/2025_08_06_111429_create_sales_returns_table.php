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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('pos_bill_id')->nullable();
            $table->decimal('total', 15, 2);
            $table->enum('refund_method', ['cash', 'debt'])->default('cash');
            $table->unsignedBigInteger('user_id'); // الموظف الذي سجل المرتجع
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('pos_bill_id')->references('id')->on('pos_bills')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};

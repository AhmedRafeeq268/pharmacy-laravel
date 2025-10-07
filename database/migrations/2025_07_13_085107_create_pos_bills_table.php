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
        Schema::create('pos_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->integer('discount');
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('payment_status')->nullable();
            // $table->tinyInteger('is_closed_with_cashbox')->default(0);
            $table->unsignedBigInteger('finished_by')->nullable();
            $table->string('status')->default('open');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_bills');
    }
};

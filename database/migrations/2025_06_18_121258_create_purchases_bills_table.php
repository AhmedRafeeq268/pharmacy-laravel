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
        Schema::create('purchases_bills', function (Blueprint $table) {
            $table->id();
            $table->integer('total_amount');
            $table->string('currancy_type');
            $table->integer('bill_number');
            $table->timestamp('bill_date');
            $table->string('employee_receipt');
            $table->tinyInteger('adopt_bill');
            $table->string('authorized_employee');
            $table->string('manufacturer');
            $table->tinyInteger('certified_or_not');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_bills');
    }
};

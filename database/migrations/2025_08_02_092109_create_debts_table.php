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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('pos_bill_id')->constrained('pos_bills')->onDelete('cascade');

            $table->decimal('total_amount', 10, 2); // كامل مبلغ الدين
            $table->decimal('remaining_amount', 10, 2); // المبلغ المتبقي بعد السداد الجزئي
            $table->enum('status', ['open', 'closed'])->default('open');

            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};

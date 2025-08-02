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
        Schema::create('debt_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_id')->constrained('debts')->onDelete('cascade');

            $table->decimal('amount_paid', 10, 2); // مبلغ هذه الدفعة
            $table->timestamp('payment_date')->useCurrent();          // تاريخ الدفعة

            $table->foreignId('paid_by')->nullable()->constrained('users'); // الموظف الذي استلم الدفعة
            $table->enum('payment_method', ['cash', 'visa'])->default('cash');
            $table->text('notes')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_payments');
    }
};

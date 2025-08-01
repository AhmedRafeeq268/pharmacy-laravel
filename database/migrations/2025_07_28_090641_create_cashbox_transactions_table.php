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
        Schema::create('cashbox_transactions', function (Blueprint $table) {
            $table->id();
            // الجلسة المرتبطة
            $table->foreignId('session_id')->constrained('pos_sessions')->cascadeOnDelete();

            // نوع الحركة: sale, refund, expense
            $table->enum('type', ['sale', 'refund', 'expense','in','out']);

            // المبلغ (موجب أو سالب حسب نوع الحركة)
            $table->decimal('amount', 12, 2);

            // ملاحظة (اختياري)
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashboxTransactions');
    }
};

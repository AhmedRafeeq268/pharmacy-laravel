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
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            // المستخدم الذي فتح الجلسة
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // وقت فتح الجلسة
            $table->dateTime('opened_at')->nullable();

            // وقت إغلاق الجلسة (nullable إذا لم تغلق بعد)
            $table->dateTime('closed_at')->nullable();

            // حالة الجلسة: مفتوحة أو مغلقة
            $table->enum('status', ['open', 'closed'])->default('open');

            // رصيد الصندوق عند فتح الجلسة
            $table->decimal('opening_balance', 12, 2)->default(0);

            // رصيد الصندوق عند الإغلاق (nullable إذا لم تغلق بعد)
            $table->decimal('closing_balance', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sessions');
    }
};

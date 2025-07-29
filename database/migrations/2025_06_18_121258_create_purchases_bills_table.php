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
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->decimal('paid', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('remaining', 15, 2)->default(0);
            $table->enum('status', ['paid', 'partial', 'unpaid'])->default('paid');

            $table->string('currancy_type', 10)->default('USD'); // تصحيح التهجئة وإضافة طول
            $table->integer('bill_number')->unique(); // إذا مطلوب رقم فاتورة فريد
            $table->timestamp('bill_date');
            $table->string('employee_receipt', 191)->nullable();
            $table->boolean('adopt_bill')->default(false); // تعديل لنوع منطقي
            $table->foreignId('authorized_employee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('manufacturer', 191)->nullable();
            $table->boolean('certified')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases_bills');
    }
};

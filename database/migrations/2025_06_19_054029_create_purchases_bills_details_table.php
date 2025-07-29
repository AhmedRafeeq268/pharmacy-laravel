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

            $table->foreignId('bill_id')->constrained('purchase_invoices')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');

            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2); // unit_price * quantity
            $table->decimal('cost', 10, 2)->nullable(); // إن كانت تكلفة الشراء تختلف عن سعر البيع

            // أرشفة بعض البيانات من المنتج لحظة الفاتورة
            $table->string('product_name')->nullable();
            $table->string('product_category')->nullable();
            $table->string('product_data')->nullable();

            $table->decimal('discount', 10, 2)->default(0);

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

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
            $table->string('name'); // اسم المنتج

            $table->integer('barcode')->unique()->nullable(); // باركود اختياري مع فهرس

            $table->string('manufacture_company'); // الشركة المصنعة

            $table->decimal('price_sell', 10, 2)->default(0);

            $table->integer('unit_price')->nullable(); // سعر الوحدة (يمكن تغييره إلى decimal إذا أردت)

            $table->integer('quantity')->default(0);

            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // تصنيف المنتج

            $table->string('image_path')->nullable(); // مسار الصورة (اختياري)

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

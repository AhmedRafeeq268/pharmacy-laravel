<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pos_bill_details', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->after('unit_price')->default(0);
            $table->decimal('profit', 10, 2)->after('price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_bill_details', function (Blueprint $table) {
            //
        });
    }
};

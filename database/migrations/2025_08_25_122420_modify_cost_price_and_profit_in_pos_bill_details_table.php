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
            $table->decimal('cost_price', 15, 2)->change();
            $table->decimal('profit', 15, 2)->change();
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

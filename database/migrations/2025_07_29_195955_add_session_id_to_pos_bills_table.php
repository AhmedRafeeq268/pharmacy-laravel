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
        Schema::table('pos_bills', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable()->after('id');

            $table->foreign('session_id')->references('id')->on('pos_sessions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_bills', function (Blueprint $table) {
            //
        });
    }
};

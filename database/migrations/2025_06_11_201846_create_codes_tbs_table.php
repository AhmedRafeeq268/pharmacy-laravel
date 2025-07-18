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
        Schema::create('codes_tbs', function (Blueprint $table) {
            $table->id();
            $table->integer('main_cd');
            $table->integer('sub_cd');
            $table->string('desc_ar');
            $table->string('desc_en');
            $table->string('details')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_editables')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes_tbs');
    }
};

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
        Schema::create('aturan_satelit', function (Blueprint $table) {
            $table->id();
            $table->string('kdtype')->nullable();
            $table->string('typektg');
            $table->string('jenisdarah');
            $table->integer('satelit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_satelit');
    }
};

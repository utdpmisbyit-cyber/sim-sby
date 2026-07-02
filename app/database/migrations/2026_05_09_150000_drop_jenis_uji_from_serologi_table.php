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
        Schema::table('serologi', function (Blueprint $table) {
            if (Schema::hasColumn('serologi', 'jenis_uji')) {
                $table->dropColumn('jenis_uji');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serologi', function (Blueprint $table) {
            if (!Schema::hasColumn('serologi', 'jenis_uji')) {
                $table->string('jenis_uji')->nullable()->after('tanggal');
            }
        });
    }
};

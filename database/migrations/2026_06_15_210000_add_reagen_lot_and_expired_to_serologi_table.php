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
            $table->string('no_lot_reagen')->nullable()->after('reagen_serologi_id');
            $table->date('tanggal_expired_reagen')->nullable()->after('no_lot_reagen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serologi', function (Blueprint $table) {
            $table->dropColumn(['no_lot_reagen', 'tanggal_expired_reagen']);
        });
    }
};

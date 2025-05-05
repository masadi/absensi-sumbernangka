<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jam', function (Blueprint $table) {
            $table->time('scan_istirahat_start', 0)->nullable();
            $table->time('scan_istirahat_end', 0)->nullable();
            $table->time('waktu_akhir_istirahat', 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jam', function (Blueprint $table) {
            $table->dropColumn(['scan_istirahat_start', 'scan_istirahat_end', 'waktu_akhir_istirahat']);
        });
    }
};

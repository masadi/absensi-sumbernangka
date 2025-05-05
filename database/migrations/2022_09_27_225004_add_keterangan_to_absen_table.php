<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganToAbsenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absen', function (Blueprint $table) {
            $table->string('keterangan')->nullable();
            $table->string('dokumen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absen', function (Blueprint $table) {
            $table->dropColumn('keterangan');
            $table->dropColumn('dokumen');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuruBpToRombonganBelajarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rombongan_belajar', function (Blueprint $table) {
            $table->uuid('bp_id')->nullable();
            $table->foreign('bp_id')->references('ptk_id')->on('ptk')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('sekolah', function (Blueprint $table) {
            $table->uuid('bp_id')->nullable();
            $table->foreign('bp_id')->references('ptk_id')->on('ptk')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rombongan_belajar', function (Blueprint $table) {
            $table->dropForeign(['bp_id']);
            $table->dropColumn('bp_id');
        });
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropForeign(['bp_id']);
            $table->dropColumn('bp_id');
        });
    }
}

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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id('id');
            $table->string('nama')->nullable();
            $table->date('tanggal')->nullable();
            $table->uuid('ptk_id');
            $table->uuid('rombongan_belajar_id');
            $table->timestamps();
            $table->foreign('ptk_id')->references('ptk_id')->on('ptk')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('rombongan_belajar_id')->references('rombongan_belajar_id')->on('rombongan_belajar')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $table->unsignedBigInteger('jadwal_id')->nullable();
            $table->foreign('jadwal_id')->references('id')->on('jadwal')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $table->dropForeign(['jadwal_id']);
            $table->dropColumn('jadwal_id');
        });
        Schema::dropIfExists('jadwal');
    }
};

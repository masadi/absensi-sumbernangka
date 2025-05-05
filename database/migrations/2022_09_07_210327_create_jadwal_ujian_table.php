<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalUjianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_ujian', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 10);
            $table->uuid('rombongan_belajar_id');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('hari_id')->constrained('nama_hari')->onUpdate('cascade')->onDelete('cascade');
            $table->smallInteger('jam_ke');
            $table->timestamps();
            $table->foreign('rombongan_belajar_id')->references('rombongan_belajar_id')->on('rombongan_belajar')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_ujian');
    }
}

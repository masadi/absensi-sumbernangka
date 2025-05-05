<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriLiburTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kategori_libur', function (Blueprint $table) {
            $table->id();
            $table->uuid('sekolah_id')->nullable();
            $table->string('tahun_ajaran_id', 4);
            $table->string('nama');
            $table->string('slug');
            $table->timestamps();
            $table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah');
            //$table->foreign('tahun_ajaran_id')->references('tahun_ajaran_id')->on('tahun_ajaran');
            //$table->foreign('rombongan_belajar_id')->references('rombongan_belajar_id')->on('rombongan_belajar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kategori_libur');
    }
}

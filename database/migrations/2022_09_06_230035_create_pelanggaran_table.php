<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelanggaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->uuid('anggota_rombel_id');
            $table->date('tanggal');
            $table->time('waktu');
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('masalah');
            $table->text('tindak_lanjut')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->foreign('anggota_rombel_id')->references('anggota_rombel_id')->on('anggota_rombel')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pelanggaran');
    }
}

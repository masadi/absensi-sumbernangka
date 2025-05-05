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
        Schema::create('jawaban_pd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jawaban_soal_id')->constrained('jawaban_soal')->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('anggota_rombel_id');
            $table->unsignedSmallInteger('nomor_jawaban');
            $table->string('jawaban', 1);
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
        Schema::dropIfExists('jawaban_pd');
    }
};

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
        Schema::create('soal_ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_id')->constrained('jadwal_ujian')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedSmallInteger('status')->default(0);
            $table->unsignedSmallInteger('jumlah_soal');
            $table->unsignedSmallInteger('jumlah_pg');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('soal_ujian');
    }
};

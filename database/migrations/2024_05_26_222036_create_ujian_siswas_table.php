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
        Schema::create('ujian_siswa', function (Blueprint $table) {
            $table->uuid('ujian_siswa_id');
            $table->foreignId('soal_ujian_id')->constrained('soal_ujian')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('waktu', 10);
            $table->unsignedSmallInteger('status')->default(0);
            $table->timestamps();
            $table->primary('ujian_siswa_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ujian_siswa');
    }
};

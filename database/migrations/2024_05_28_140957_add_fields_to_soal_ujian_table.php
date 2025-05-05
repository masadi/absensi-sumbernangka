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
        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->unsignedInteger('skor_benar')->nullable()->default(2);
            $table->unsignedInteger('skor_salah')->nullable()->default(0);
            $table->unsignedInteger('skala_nilai')->nullable()->default(100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('soal_ujian', function (Blueprint $table) {
            $table->dropColumn(['skor_benar', 'skor_salah', 'skala_nilai']);
        });
    }
};

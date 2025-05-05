<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiburTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('libur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_libur')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title', 100);
            $table->timestamp('mulai_tanggal');
            $table->timestamp('sampai_tanggal');
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
        Schema::dropIfExists('libur');
    }
}

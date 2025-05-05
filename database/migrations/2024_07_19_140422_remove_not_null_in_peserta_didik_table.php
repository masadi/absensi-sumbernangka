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
        Schema::table('peserta_didik', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable()->change();
			$table->date('tanggal_lahir')->nullable()->change();
			$table->integer('agama_id')->nullable()->change();
            $table->string('kode_wilayah', 8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('peserta_didik', function (Blueprint $table) {
            //
        });
    }
};

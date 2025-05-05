<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeConstrainInJadwalUjianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jadwal_ujian', function (Blueprint $table) {
            $doctrineTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys('jadwal_ujian');
            foreach ($doctrineTable as $foreignKey) {
                if($foreignKey->getForeignTableName() == 'nama_hari'){
                    $table->dropForeign(['hari_id']);
                }
            }
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
            //$table->foreignId('hari_id')->constrained('nama_hari')->onUpdate('cascade')->onDelete('cascade');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteUserIdFromUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('peserta_didik_id')->nullable();
            $table->uuid('ptk_id')->nullable();
            $table->foreign('peserta_didik_id')->references('peserta_didik_id')->on('peserta_didik')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('ptk_id')->references('ptk_id')->on('ptk')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('peserta_didik', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
        Schema::table('ptk', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['peserta_didik_id']);
            $table->dropColumn('peserta_didik_id');
            $table->dropForeign(['ptk_id']);
            $table->dropColumn('ptk_id');
        });
        Schema::table('peserta_didik', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained();
        });
        Schema::table('ptk', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('perfil_id')->unsigned()->nullable()->after('id');
            $table->foreign('perfil_id')->references('idperfil')->on('perfil')->onDelete('set null');
        });
    }

    public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['perfil_id']);
        $table->dropColumn('perfil_id');
    });
}




};

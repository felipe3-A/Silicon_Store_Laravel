<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('person_id')->nullable()->after('id'); // Asegura que sea un `unsignedInteger`
            $table->foreign('person_id')->references('person_id')->on('ospos_people')->onDelete('set null');
        });
    }

    public function down() {
        Schema::table('users', callback: function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->dropColumn('person_id');
        });
    }
};

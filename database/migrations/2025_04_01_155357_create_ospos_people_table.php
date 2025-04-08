<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('ospos_people', function (Blueprint $table) {
            $table->id('person_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('gender')->nullable();
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->string('address_1');
            $table->string('address_2')->nullable()->change();
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('country');
            $table->text('comments');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ospos_people');
    }
};

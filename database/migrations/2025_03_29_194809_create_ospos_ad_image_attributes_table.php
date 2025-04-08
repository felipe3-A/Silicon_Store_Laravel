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
    Schema::create('ospos_ad_image_attributes', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('ad_image_id');
        $table->string('attribute_name');
        $table->string('attribute_value');
        $table->timestamps();

        $table->foreign('ad_image_id')->references('id')->on('ospos_ad_images')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ospos_ad_image_attributes');
    }
};

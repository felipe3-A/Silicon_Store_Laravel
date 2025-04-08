<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ospos_ad_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_url'); // URL de la imagen
            $table->text('description')->nullable(); // Descripción opcional
            $table->timestamps();
        });

        Schema::create('ospos_ad_image_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_image_id')->constrained('ospos_ad_images')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('ad_image_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_image_item_id')->constrained('ospos_ad_image_items')->onDelete('cascade');
            $table->string('attribute_name');
            $table->string('attribute_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_image_attributes');
        Schema::dropIfExists('ospos_ad_image_items');
        Schema::dropIfExists('ospos_ad_images');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('store_shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->integer('person_id');
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('person_id')->references('person_id')->on('ospos_people')->onDelete('cascade');
        });

    }

    public function down(): void {
        Schema::dropIfExists('store_shopping_carts');
    }
};

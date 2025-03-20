<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('store_cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->integer('item_id');
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('cart_id')->references('id')->on('store_shopping_carts')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('ospos_items')->onDelete('cascade');
        });

    }

    public function down(): void {
        Schema::dropIfExists('store_cart_items');
    }
};

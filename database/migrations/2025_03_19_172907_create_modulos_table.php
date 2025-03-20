
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_modulo_padre')->nullable();
            $table->string('modulo');
            $table->string('url_modulo');
            $table->string('icono')->nullable();
            $table->integer('orden');
            $table->timestamps();

            $table->foreign('id_modulo_padre')->references('id')->on('modulos')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('modulos');
    }
};

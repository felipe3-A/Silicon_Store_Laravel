<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('modulos_x_perfil', function (Blueprint $table) {
            $table->bigIncrements('id'); // ID autoincremental
            $table->unsignedBigInteger('idmodulo'); // Relación con la tabla módulos
            $table->unsignedBigInteger('idperfil'); // Relación con la tabla perfil
            $table->string('permiso'); // Permiso asignado (por ejemplo, "lectura", "escritura")

            $table->timestamps();

            // Definir claves foráneas
            $table->foreign('idmodulo')->references('id')->on('modulos')->onDelete('cascade');
            $table->foreign('idperfil')->references('idperfil')->on('perfil')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('modulos_x_perfil');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('perfil', function (Blueprint $table) {
            $table->bigIncrements('idperfil'); // Definir idperfil como clave primaria
            $table->string('perfil')->unique(); // Nombre del perfil, único
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('perfiles');
    }
};

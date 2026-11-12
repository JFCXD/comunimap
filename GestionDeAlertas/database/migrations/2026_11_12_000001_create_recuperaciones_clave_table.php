<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recuperaciones_clave', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idUsuario')->unique();
            $table->char('token_hash', 64);
            $table->enum('metodo', ['correo', 'administrador']);
            $table->unsignedBigInteger('administrador_id')->nullable();
            $table->timestamp('vence_en');
            $table->timestamps();
            $table->index('vence_en');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recuperaciones_clave');
    }
};

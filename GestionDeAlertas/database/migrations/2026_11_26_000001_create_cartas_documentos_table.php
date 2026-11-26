<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cartas_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->nullable()->unique();
            $table->enum('estado', ['Borrador', 'Emitida'])->default('Borrador')->index();
            $table->string('asunto', 250)->nullable();
            $table->longText('datos_json');
            $table->longText('reportes_json');
            $table->unsignedBigInteger('creado_por');
            $table->timestamp('emitida_en')->nullable();
            $table->timestamps();
            $table->index(['creado_por', 'created_at']);
        });
        // Railway utiliza MySQL: guardar la versión PDF fija dentro de la BD.
        \\Illuminate\\Support\\Facades\\DB::statement('ALTER TABLE cartas_documentos ADD COLUMN pdf_binario LONGBLOB NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('cartas_documentos');
    }
};

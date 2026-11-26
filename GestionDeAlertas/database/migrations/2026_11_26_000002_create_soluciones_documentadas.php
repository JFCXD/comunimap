<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soluciones_documentadas', function (Blueprint $table) {
            $table->id();
            $table->integer('reporte_representativo');
            $table->json('reportes_ids');
            $table->text('explicacion');
            $table->string('responsable', 180)->nullable();
            $table->string('foto_despues', 255);
            $table->json('anexos')->nullable();
            $table->unsignedInteger('registrado_por');
            $table->timestamp('created_at')->useCurrent();
            $table->index('reporte_representativo');
        });
        Schema::create('solucion_reporte', function (Blueprint $table) {
            $table->unsignedBigInteger('solucion_id');
            $table->integer('reporte_id');
            $table->primary(['solucion_id', 'reporte_id']);
            $table->unique('reporte_id');
            $table->foreign('solucion_id')->references('id')->on('soluciones_documentadas')->cascadeOnDelete();
            $table->foreign('reporte_id')->references('idReporte')->on('reportes')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solucion_reporte');
        Schema::dropIfExists('soluciones_documentadas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            // NULL para conservar todas las notificaciones actuales de reportes.
            $table->unsignedBigInteger('idPublicacion')->nullable();
            // MySQL permite múltiples NULL: los avisos existentes no se ven afectados.
            $table->unique(['idUsuario', 'idPublicacion'], 'uq_notif_comunidad_usuario_publicacion');
        });
    }

    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropUnique('uq_notif_comunidad_usuario_publicacion');
            $table->dropColumn('idPublicacion');
        });
    }
};

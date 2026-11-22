<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

 
// Prueba aislada de Gmail API: no cambia RECOVERY_MAIL_PROVIDER ni los tokens
// de recuperación de los vecinos. Ejecutar solo desde la consola del servidor.
Artisan::command('comunimap:probar-gmail {destinatario}', function (string $destinatario) {
    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        $this->error('Dirección de correo no válida');
        return 1;
    }

    try {
        app(\App\Services\CorreoRecuperacionGmail::class)->enviar(
            $destinatario,
            'ComuniMap - Prueba Gmail API',
            "Esta es una prueba de envío HTTPS desde Laravel en Railway.\n"
            ."No es un enlace de recuperación de contraseña."
        );
        $this->info('Google aceptó el correo de prueba. Verifica la recepción.');
        return 0;
    } catch (\Throwable $e) {
        // No imprimir la excepción: el SDK HTTP podría incluir detalles sensibles.
        $this->error('No se pudo enviar con Gmail API. Revisa la configuración y OAuth.');
        return 1;
    }
})->purpose('Prueba Gmail API desde Laravel sin alterar el proveedor activo');

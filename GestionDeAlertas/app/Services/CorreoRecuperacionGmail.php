<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Envía exclusivamente correos de recuperación mediante Gmail API (HTTPS).
 * Las credenciales OAuth se leen desde la configuración privada del servidor.
 */
class CorreoRecuperacionGmail
{
    public function enviar(string $destinatario, string $asunto, string $cuerpo): void
    {
        $config = config('services.gmail_api', []);
        $remitente = $config['remitente'] ?? '';
        $clientId = $config['client_id'] ?? '';
        $clientSecret = $config['client_secret'] ?? '';
        $refreshToken = $config['refresh_token'] ?? '';

        if (!filter_var($remitente, FILTER_VALIDATE_EMAIL) ||
            !filter_var($destinatario, FILTER_VALIDATE_EMAIL) ||
            !$clientId || !$clientSecret || !$refreshToken) {
            throw new RuntimeException('Falta la configuración privada de Gmail API');
        }

        // Obtener un token de acceso temporal a partir del refresh token.
        $respuestaToken = Http::asForm()->acceptJson()->timeout(12)
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

        $accessToken = $respuestaToken->json('access_token');
        if (!$respuestaToken->successful() || !is_string($accessToken) || $accessToken === '') {
            // Nunca incluir la respuesta de Google: podría contener datos sensibles.
            throw new RuntimeException('Google OAuth no entregó un token de acceso');
        }

        $asuntoCodificado = '=?UTF-8?B?'.base64_encode($asunto).'?=';
        $texto = str_replace(["\r\n", "\r"], "\n", $cuerpo);
        $texto = str_replace("\n", "\r\n", $texto);
        $mime = "From: ComuniMap <{$remitente}>\r\n"
            ."To: {$destinatario}\r\n"
            ."Subject: {$asuntoCodificado}\r\n"
            ."MIME-Version: 1.0\r\n"
            ."Content-Type: text/plain; charset=UTF-8\r\n"
            ."Content-Transfer-Encoding: 8bit\r\n\r\n"
            .$texto;

        $raw = rtrim(strtr(base64_encode($mime), '+/', '-_'), '=');

        $respuestaEnvio = Http::withToken($accessToken)->acceptJson()->timeout(15)
            ->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                'raw' => $raw,
            ]);

        if (!$respuestaEnvio->successful() || !is_string($respuestaEnvio->json('id'))) {
            throw new RuntimeException('Gmail API no aceptó el mensaje');
        }
    }
}

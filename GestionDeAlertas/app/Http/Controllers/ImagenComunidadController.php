<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Imágenes de Comunidad en el volumen persistente YA montado en Laravel.
 * No utiliza storage:link ni cambia la ubicación de imágenes de reportes.
 */
class ImagenComunidadController extends Controller
{
    public static function directorio(): string
    {
        $montaje = (string) env('RAILWAY_VOLUME_MOUNT_PATH', '');
        if ($montaje === '' || !str_starts_with($montaje, '/') || !is_dir($montaje) || !is_writable($montaje)) {
            throw ValidationException::withMessages([
                'imagen' => 'El volumen persistente de imágenes no está disponible en Railway.'
            ]);
        }
        $directorio = rtrim($montaje, '/') . '/comunidad';
        if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
            throw ValidationException::withMessages(['imagen' => 'No se pudo preparar el directorio permanente.']);
        }
        if (!is_writable($directorio)) {
            throw ValidationException::withMessages(['imagen' => 'El directorio permanente no permite escribir.']);
        }
        return $directorio;
    }

    public static function guardarArchivo(\Illuminate\Http\UploadedFile $archivo): string
    {
        $extension = match ($archivo->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
        if (!$extension || !$archivo->isValid()) {
            throw ValidationException::withMessages(['imagen' => 'Selecciona una imagen JPG, PNG o WEBP válida.']);
        }
        $nombre = bin2hex(random_bytes(20)) . '.' . $extension;
        $archivo->move(self::directorio(), $nombre);
        return 'comunidad/' . $nombre;
    }

    public static function eliminarArchivo(string $ruta): void
    {
        if (!preg_match('/^comunidad\/[a-f0-9]{40}\.(jpg|png|webp)$/D', $ruta)) {
            return; // Nunca tocar fotografías anteriores u otros directorios.
        }
        $montaje = (string) env('RAILWAY_VOLUME_MOUNT_PATH', '');
        if ($montaje === '' || !str_starts_with($montaje, '/')) return;
        $archivo = rtrim($montaje, '/') . '/' . $ruta;
        if (is_file($archivo)) @unlink($archivo);
    }

    public function mostrar(string $nombre)
    {
        if (!preg_match('/^[a-f0-9]{40}\.(jpg|png|webp)$/D', $nombre)) abort(404);
        $ruta = 'comunidad/' . $nombre;
        if (!DB::table('publicaciones')->where('imagen', $ruta)->exists()) abort(404);
        $montaje = (string) env('RAILWAY_VOLUME_MOUNT_PATH', '');
        $archivo = rtrim($montaje, '/') . '/' . $ruta;
        if (!is_file($archivo)) abort(404);
        return response()->file($archivo, [
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function subirDesdeApi(Request $request)
    {
        $secreto = (string) config('services.comunimap.image_upload_secret', '');
        $recibido = (string) $request->header('X-Comunimap-Image-Secret', '');
        if (strlen($secreto) < 32 || !hash_equals($secreto, $recibido)) abort(403);

        $datos = $request->validate([
            'idPublicacion' => ['required', 'integer', 'min:1'],
            'imagen' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);
        $id = (int) $datos['idPublicacion'];

        // No permitir reemplazar fotografías existentes ni cambiar publicaciones archivadas.
        $publicacion = DB::table('publicaciones')->where('idPublicacion', $id)->first();
        if (!$publicacion) abort(404);
        if ($publicacion->imagen !== null || $publicacion->estado === 'Archivado') {
            return response()->json(['success' => false, 'message' => 'Esta publicación ya tiene imagen o está archivada.'], 409);
        }

        $ruta = self::guardarArchivo($request->file('imagen'));
        try {
            $actualizadas = DB::table('publicaciones')
                ->where('idPublicacion', $id)->whereNull('imagen')
                ->where('estado', '<>', 'Archivado')
                ->update(['imagen' => $ruta]);
            if (!$actualizadas) {
                self::eliminarArchivo($ruta);
                return response()->json(['success' => false, 'message' => 'La publicación cambió mientras se adjuntaba la imagen.'], 409);
            }
        } catch (\Throwable $e) {
            self::eliminarArchivo($ruta);
            throw $e;
        }

        return response()->json([
            'success' => true,
            'imagen' => $ruta,
            'imagenUrl' => route('comunidad.imagen', ['nombre' => basename($ruta)]),
        ]);
    }
}

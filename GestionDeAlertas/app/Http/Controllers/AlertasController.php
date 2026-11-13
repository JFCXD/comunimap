<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AlertasController extends Controller
{
    public function index()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        $categorias = DB::table('categorias')
            ->select('idCategoria', 'nombre')
            ->orderBy('idCategoria')
            ->get();

        return view('alertas.alertas', compact('categorias'));
    }

    public function guardar(Request $request)
    {
        // =====================================================
        // VALIDAR SESIÓN
        // =====================================================

        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        // Validar en el servidor: no confiar únicamente en los campos HTML.
        // Los límites corresponden a los formularios de reportes de ComuniMap.
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'min:3', 'max:150'],
            'descripcion' => ['required', 'string', 'min:10', 'max:3000'],
            'direccion' => ['required', 'string', 'min:3', 'max:255'],
            'idCategoria' => ['required', 'integer', 'exists:categorias,idCategoria'],
            'ubicacion' => ['required', 'string', 'max:100'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'titulo.required' => 'Ingresa el título del problema.',
            'titulo.min' => 'El título debe tener al menos 3 caracteres.',
            'descripcion.required' => 'Describe el problema.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'direccion.required' => 'Ingresa una dirección o referencia.',
            'idCategoria.exists' => 'Selecciona una categoría válida.',
            'ubicacion.required' => 'Selecciona la ubicación en el mapa.',
            'imagen.image' => 'La evidencia debe ser una imagen válida.',
            'imagen.max' => 'La fotografía no debe superar 4 MB.',
        ]);

        // =====================================================
        // DATOS DE UBICACIÓN
        // =====================================================

        $ubicacion = trim($datos['ubicacion']);

        $latitud = null;
        $longitud = null;

        // La ubicación actualmente llega como:
        // -17.437995,-66.146491
        if (!empty($ubicacion) && str_contains($ubicacion, ',')) {

            $coordenadas = explode(',', $ubicacion);

            if (count($coordenadas) >= 2) {

                $latitud = trim($coordenadas[0]);
                $longitud = trim($coordenadas[1]);

                // Validamos que realmente sean números
                if (!is_numeric($latitud) || !is_numeric($longitud)) {
                    $latitud = null;
                    $longitud = null;
                }
            }
        }

        // =====================================================
        // GEOCERCA OTB: VALIDACIÓN OBLIGATORIA EN EL SERVIDOR
        // =====================================================

        if ($latitud === null || $longitud === null
            || (float) $latitud < -90 || (float) $latitud > 90
            || (float) $longitud < -180 || (float) $longitud > 180) {
            return back()->withInput()->withErrors([
                'ubicacion' => 'Selecciona una ubicación válida dentro de la OTB.'
            ]);
        }

        $rutaLimite = public_path('otb_limite.geojson');
        $contenidoLimite = @file_get_contents($rutaLimite);
        $geojson = $contenidoLimite === false ? null : json_decode($contenidoLimite, true);
        $anillo = $geojson['features'][0]['geometry']['coordinates'][0] ?? null;

        // Si falta el polígono, se impide registrar reportes en lugar de omitir la protección.
        if (!is_array($anillo) || count($anillo) < 4) {
            return back()->withInput()->withErrors([
                'ubicacion' => 'No están disponibles los límites de la OTB. Intenta más tarde.'
            ]);
        }

        $x = (float) $longitud;
        $y = (float) $latitud;
        $dentro = false;
        $n = count($anillo) - 1;
        for ($i = 0; $i < $n; $i++) {
            if (!is_array($anillo[$i]) || !is_array($anillo[$i + 1])
                || count($anillo[$i]) < 2 || count($anillo[$i + 1]) < 2) {
                return back()->withInput()->withErrors([
                    'ubicacion' => 'Los límites de la OTB no son válidos.'
                ]);
            }

            $x1 = (float) $anillo[$i][0]; $y1 = (float) $anillo[$i][1];
            $x2 = (float) $anillo[$i + 1][0]; $y2 = (float) $anillo[$i + 1][1];
            $cruz = ($x - $x1) * ($y2 - $y1) - ($y - $y1) * ($x2 - $x1);

            if (abs($cruz) <= 1e-11
                && $x >= min($x1, $x2) - 1e-10 && $x <= max($x1, $x2) + 1e-10
                && $y >= min($y1, $y2) - 1e-10 && $y <= max($y1, $y2) + 1e-10) {
                $dentro = true;
                break;
            }

            if (($y1 > $y) !== ($y2 > $y)) {
                $cruceX = $x1 + ($y - $y1) * ($x2 - $x1) / ($y2 - $y1);
                if ($x < $cruceX) $dentro = !$dentro;
            }
        }

        if (!$dentro) {
            return back()->withInput()->withErrors([
                'ubicacion' => 'Esta ubicación está fuera de los límites de la OTB.'
            ]);
        }

        // =====================================================
        // IMAGEN OPCIONAL
        // =====================================================

        $rutaImagen = null;

        if ($request->hasFile('imagen')) {

            $imagen = $request->file('imagen');

            // Todas las evidencias se guardan en reportes/
            $rutaImagen = $imagen->store(
                'reportes',
                'public'
            );
        }

        // =====================================================
        // GUARDAR REPORTE
        // =====================================================

        // Guardar reporte y avisos internos juntos para evitar registros incompletos.
        $idsAvisos = DB::transaction(function () use ($datos, $ubicacion, $latitud, $longitud, $rutaImagen) {
            $idReporte = DB::table('reportes')->insertGetId([
                'titulo' => trim($datos['titulo']),
                'descripcion' => trim($datos['descripcion']),
                'ubicacion' => $ubicacion,
                'latitud' => $latitud,
                'longitud' => $longitud,
                'direccion' => trim($datos['direccion']),
                'estado' => 'Pendiente',
                'prioridad' => null,
                'idUsuario' => Session::get('idUsuario'),
                'idCategoria' => (int) $datos['idCategoria'],
                'imagen' => $rutaImagen,
            ], 'idReporte');

            $ids = [];
            $administradores = DB::table('usuarios')
                ->where('rol', 'Administrador')
                ->where('estado', 'Activo')
                ->where('idUsuario', '!=', Session::get('idUsuario'))
                ->pluck('idUsuario');

            foreach ($administradores as $idAdmin) {
                $ids[] = (int) DB::table('notificaciones')->insertGetId([
                    'idUsuario' => (int) $idAdmin,
                    'idReporte' => $idReporte,
                    'titulo' => 'Nuevo reporte',
                    'mensaje' => mb_substr('Se registró un nuevo reporte en ComuniMap: ' . trim($request->titulo), 0, 255),
                    'tipo' => 'NUEVO_REPORTE',
                    'leida' => 0,
                ], 'idNotificacion');
            }
            return $ids;
        });

        // Enviar push fuera de la transacción. El reporte sigue guardado si Firebase falla.
        $secretoPush = (string) config('services.comunimap.web_push_secret', '');
        if ($idsAvisos && strlen($secretoPush) >= 32) {
            foreach (array_chunk($idsAvisos, 50) as $lote) {
                try {
                    $respuesta = \Illuminate\Support\Facades\Http::timeout(12)
                        ->withHeaders(['X-Comunimap-Web-Push-Secret' => $secretoPush])
                        ->post('https://comunimap-api-production.up.railway.app/push_notificaciones_web.php', [
                            'idsNotificaciones' => $lote,
                        ]);
                    if (!$respuesta->successful()) {
                        \Log::warning('ComuniMap: fallo push de nuevo reporte', ['status' => $respuesta->status()]);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('ComuniMap: no se pudo enviar push de nuevo reporte', ['error' => $e->getMessage()]);
                }
            }
        }

        return redirect('/alertas');
    }
}
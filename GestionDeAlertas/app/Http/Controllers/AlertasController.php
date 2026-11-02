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

        // =====================================================
        // DATOS DE UBICACIÓN
        // =====================================================

        $ubicacion = trim($request->ubicacion);

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

        DB::insert("
            INSERT INTO reportes
            (
                titulo,
                descripcion,
                ubicacion,
                latitud,
                longitud,
                direccion,
                estado,
                prioridad,
                idUsuario,
                idCategoria,
                imagen
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ", [
            trim($request->titulo),
            trim($request->descripcion),
            $ubicacion,
            $latitud,
            $longitud,
            $request->direccion,
            'Pendiente',
            null,
            Session::get('idUsuario'),
            $request->idCategoria,
            $rutaImagen
        ]);

        return redirect('/alertas');
    }
}
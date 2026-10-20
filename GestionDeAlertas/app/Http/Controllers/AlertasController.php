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

        return view('alertas.alertas');
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
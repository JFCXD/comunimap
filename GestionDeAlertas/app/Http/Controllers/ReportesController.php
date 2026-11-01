<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE REPORTES Y COINCIDENCIAS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        // Todos los reportes se usan para calcular coincidencias,
        // aunque un usuario normal solo vea sus propios reportes.
        $todosReportes = DB::select("
            SELECT
                idReporte,
                estado,
                idUsuario,
                idCategoria,
                latitud,
                longitud
            FROM reportes
        ");

        // Mantener la separación entre administrador y vecino.
        $sql = "
            SELECT
                r.idReporte,
                r.titulo,
                r.imagen,
                r.estado,
                r.idUsuario,
                r.idCategoria,
                r.latitud,
                r.longitud,
                r.fechaCreacion,
                c.nombre,
                u.nombre AS usuario
            FROM reportes r
            INNER JOIN categorias c
                ON r.idCategoria = c.idCategoria
            INNER JOIN usuarios u
                ON r.idUsuario = u.idUsuario
        ";

        if (Session::get('rol') === 'Administrador') {
            $reportes = DB::select(
                $sql . " ORDER BY r.idReporte DESC"
            );
        } else {
            $reportes = DB::select(
                $sql . "
                    WHERE r.idUsuario = ?
                    ORDER BY r.idReporte DESC
                ",
                [Session::get('idUsuario')]
            );
        }

        // Calcular coincidencias para cada reporte visible.
        foreach ($reportes as $reporte) {

            // Los reportes solucionados no participan.
            if ($this->estaSolucionado($reporte->estado)) {
                $reporte->cantidadCoincidencias = 0;
                $reporte->nivelCoincidencia = 'No aplica';
                continue;
            }

            // Sin coordenadas no se puede calcular distancia.
            if (
                $reporte->latitud === null ||
                $reporte->longitud === null
            ) {
                $reporte->cantidadCoincidencias = 0;
                $reporte->nivelCoincidencia = 'No aplica';
                continue;
            }

            $usuarios = $this->obtenerUsuariosCoincidentes(
                $reporte,
                $todosReportes
            );

            $cantidad = count($usuarios);

            $reporte->cantidadCoincidencias = $cantidad;

            $reporte->nivelCoincidencia =
                $this->obtenerNivelCoincidencia($cantidad);
        }

        return view(
            'reportes.reportes',
            compact('reportes')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DISTANCIA HAVERSINE EN METROS
    |--------------------------------------------------------------------------
    */
    private function calcularDistanciaMetros(
        float $latitud1,
        float $longitud1,
        float $latitud2,
        float $longitud2
    ): float {
        $radioTierra = 6371000;

        $diferenciaLatitud = deg2rad(
            $latitud2 - $latitud1
        );

        $diferenciaLongitud = deg2rad(
            $longitud2 - $longitud1
        );

        $latitud1Rad = deg2rad($latitud1);
        $latitud2Rad = deg2rad($latitud2);

        $a =
            sin($diferenciaLatitud / 2) ** 2
            +
            cos($latitud1Rad)
            * cos($latitud2Rad)
            * sin($diferenciaLongitud / 2) ** 2;

        // Evitar errores de precisión matemática.
        $a = min(1, max(0, $a));

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $radioTierra * $c;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCAR VECINOS COINCIDENTES
    |--------------------------------------------------------------------------
    |
    | Condiciones:
    | 1. Misma categoría.
    | 2. Distancia máxima de 5 metros.
    | 3. Reportes no solucionados.
    | 4. Cada usuario cuenta una sola vez.
    |
    */
    private function obtenerUsuariosCoincidentes(
        $reporte,
        array $todosReportes
    ): array {
        $usuariosCoincidentes = [];

        // El autor cuenta como el primer vecino.
        $usuariosCoincidentes[
            (string) $reporte->idUsuario
        ] = true;

        foreach ($todosReportes as $otroReporte) {

            // No comparar el reporte consigo mismo.
            if (
                (int) $otroReporte->idReporte ===
                (int) $reporte->idReporte
            ) {
                continue;
            }

            // Deben pertenecer a la misma categoría.
            if (
                (int) $otroReporte->idCategoria !==
                (int) $reporte->idCategoria
            ) {
                continue;
            }

            // Ignorar problemas ya solucionados.
            if ($this->estaSolucionado($otroReporte->estado)) {
                continue;
            }

            // Ignorar registros sin coordenadas.
            if (
                $otroReporte->latitud === null ||
                $otroReporte->longitud === null
            ) {
                continue;
            }

            // Calcular distancia geográfica.
            $distancia = $this->calcularDistanciaMetros(
                (float) $reporte->latitud,
                (float) $reporte->longitud,
                (float) $otroReporte->latitud,
                (float) $otroReporte->longitud
            );

            if ($distancia <= 5) {

                // Usar idUsuario como clave evita duplicados.
                $usuariosCoincidentes[
                    (string) $otroReporte->idUsuario
                ] = true;
            }
        }

        return array_keys($usuariosCoincidentes);
    }

    /*
    |--------------------------------------------------------------------------
    | NIVEL SEGÚN CANTIDAD DE VECINOS
    |--------------------------------------------------------------------------
    */
    private function obtenerNivelCoincidencia(
        int $cantidadUsuarios
    ): string {
        if ($cantidadUsuarios >= 5) {
            return 'Muy reportado';
        }

        if ($cantidadUsuarios === 4) {
            return 'Frecuente';
        }

        if ($cantidadUsuarios >= 2) {
            return 'Recurrente';
        }

        return 'Individual';
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR ESTADO SOLUCIONADO
    |--------------------------------------------------------------------------
    */
    private function estaSolucionado(?string $estado): bool
    {
        $estadoNormalizado = mb_strtolower(
            trim($estado ?? '')
        );

        return in_array(
            $estadoNormalizado,
            ['solucionado', 'resuelto'],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PDF INDIVIDUAL
    |--------------------------------------------------------------------------
    */
    public function pdf($id)
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        $reportes = DB::select("
            SELECT
                r.idReporte,
                r.titulo,
                r.descripcion,
                r.ubicacion,
                r.direccion,
                r.estado,
                r.imagen,
                c.nombre AS categoria,
                u.nombre AS usuario
            FROM reportes r
            INNER JOIN categorias c
                ON r.idCategoria = c.idCategoria
            INNER JOIN usuarios u
                ON r.idUsuario = u.idUsuario
            WHERE r.idReporte = ?
        ", [$id]);

        if (count($reportes) === 0) {
            return redirect('/reportes');
        }

        // Respetar la propiedad de los reportes.
        if (Session::get('rol') !== 'Administrador') {
            $pertenece = DB::table('reportes')
                ->where('idReporte', $id)
                ->where('idUsuario', Session::get('idUsuario'))
                ->exists();

            if (!$pertenece) {
                abort(403);
            }
        }

        $pdf = Pdf::loadView(
            'reportes.pdf',
            compact('reportes')
        );

        return $pdf->stream(
            'reporte-' . $id . '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PDF GENERAL
    |--------------------------------------------------------------------------
    */
    public function pdfTodos()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') !== 'Administrador') {
            return redirect('/inicio');
        }

        $reportes = DB::select("
            SELECT
                r.idReporte,
                r.titulo,
                c.nombre AS categoria,
                r.estado,
                u.nombre AS usuario
            FROM reportes r
            INNER JOIN categorias c
                ON r.idCategoria = c.idCategoria
            INNER JOIN usuarios u
                ON r.idUsuario = u.idUsuario
            ORDER BY r.idReporte DESC
        ");

        $pdf = Pdf::loadView(
            'reportes.pdfTodos',
            compact('reportes')
        );

        return $pdf->stream(
            'Listado_Reportes.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR REPORTE
    |--------------------------------------------------------------------------
    */
    public function editar($id)
    {
        if (Session::get('rol') !== 'Administrador') {
            return redirect('/inicio');
        }

        $reporte = DB::select("
            SELECT *
            FROM reportes
            WHERE idReporte = ?
        ", [$id]);

        $categorias = DB::select("
            SELECT *
            FROM categorias
        ");

        return view(
            'reportes.editarr',
            compact('reporte', 'categorias')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR REPORTE
    |--------------------------------------------------------------------------
    */
    public function actualizar(Request $request, $id)
    {
        if (Session::get('rol') !== 'Administrador') {
            return redirect('/inicio');
        }

        $reporte = DB::select("
            SELECT imagen
            FROM reportes
            WHERE idReporte = ?
        ", [$id]);

        if (count($reporte) === 0) {
            return redirect('/reportes');
        }

        $imagen = $reporte[0]->imagen;

        // Reemplazar fotografía, si se envía una nueva.
        if ($request->hasFile('imagen')) {

            $request->validate([
                'imagen' => [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120'
                ]
            ]);

            $nuevaImagen = $request
                ->file('imagen')
                ->store('reportes', 'public');

            $imagenAnterior = $imagen;
            $imagen = $nuevaImagen;
        }

        DB::update("
            UPDATE reportes
            SET
                titulo = ?,
                descripcion = ?,
                direccion = ?,
                estado = ?,
                idCategoria = ?,
                imagen = ?
            WHERE idReporte = ?
        ", [
            $request->titulo,
            $request->descripcion,
            $request->direccion,
            $request->estado,
            $request->idCategoria,
            $imagen,
            $id
        ]);

        // Eliminar la imagen vieja después de actualizar.
        if (
            isset($imagenAnterior) &&
            $imagenAnterior &&
            Storage::disk('public')->exists($imagenAnterior)
        ) {
            Storage::disk('public')->delete($imagenAnterior);
        }

        return redirect('/reportes');
    }

    /*
    |--------------------------------------------------------------------------
    | SUBIR IMAGEN DESDE ANDROID
    |--------------------------------------------------------------------------
    */
    public function subirImagen(Request $request)
    {
        $request->validate([
            'imagen' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120'
            ]
        ]);

        $ruta = $request
            ->file('imagen')
            ->store('reportes', 'public');

        return response()->json([
            'success' => true,
            'mensaje' => 'Imagen subida correctamente',
            'ruta' => $ruta,
            'url' => asset('storage/' . $ruta)
        ], 201);
    }
}

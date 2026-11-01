<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE REPORTES
    |--------------------------------------------------------------------------
    |
    | El usuario normal sigue viendo solamente sus propios reportes.
    | El administrador sigue viendo todos.
    |
    | La diferencia es que el cálculo de coincidencias se realiza utilizando
    | TODOS los reportes activos de la comunidad, no solamente los visibles
    | para el usuario actual.
    |
    */
    public function index()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        /*
        |--------------------------------------------------------------------------
        | Todos los reportes
        |--------------------------------------------------------------------------
        |
        | Esta colección se utiliza exclusivamente para calcular coincidencias.
        |
        */
        $todosReportes = DB::select("
            SELECT
                r.idReporte,
                r.titulo,
                r.estado,
                r.idUsuario,
                r.idCategoria,
                r.latitud,
                r.longitud
            FROM reportes r
        ");

        /*
        |--------------------------------------------------------------------------
        | Reportes visibles
        |--------------------------------------------------------------------------
        */

        if (Session::get('rol') == 'Administrador') {

            $reportes = DB::select("
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

                ORDER BY r.idReporte DESC
            ");

        } else {

            $reportes = DB::select("
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

                WHERE r.idUsuario = ?

                ORDER BY r.idReporte DESC
            ", [
                Session::get('idUsuario')
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Calcular coincidencias
        |--------------------------------------------------------------------------
        */

        foreach ($reportes as $reporte) {

            /*
             * Un reporte solucionado ya no participa en una incidencia activa.
             */
            if ($this->estaSolucionado($reporte->estado)) {

                $reporte->cantidadCoincidencias = 0;
                $reporte->nivelCoincidencia = 'No aplica';

                continue;
            }

            $usuariosCoincidentes =
                $this->obtenerUsuariosCoincidentes(
                    $reporte,
                    $todosReportes
                );

            $cantidad = count($usuariosCoincidentes);

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
    | CALCULAR DISTANCIA ENTRE DOS COORDENADAS
    |--------------------------------------------------------------------------
    |
    | Fórmula Haversine.
    | El resultado se devuelve en METROS.
    |
    */
    private function calcularDistanciaMetros(
        float $latitud1,
        float $longitud1,
        float $latitud2,
        float $longitud2
    ): float {

        $radioTierra = 6371000;

        $latitud1Rad = deg2rad($latitud1);
        $latitud2Rad = deg2rad($latitud2);

        $diferenciaLatitud =
            deg2rad($latitud2 - $latitud1);

        $diferenciaLongitud =
            deg2rad($longitud2 - $longitud1);

        $a =
            sin($diferenciaLatitud / 2)
            * sin($diferenciaLatitud / 2)
            +
            cos($latitud1Rad)
            * cos($latitud2Rad)
            * sin($diferenciaLongitud / 2)
            * sin($diferenciaLongitud / 2);

        /*
         * Protección para evitar pequeños errores de precisión.
         */
        $a = min(1, max(0, $a));

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $radioTierra * $c;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER USUARIOS COINCIDENTES
    |--------------------------------------------------------------------------
    |
    | Para contar como coincidencia:
    |
    | 1. Debe ser la misma categoría.
    | 2. Debe estar a máximo 5 metros.
    | 3. No debe estar solucionado.
    | 4. Cada usuario solamente puede contar una vez.
    |
    */
    private function obtenerUsuariosCoincidentes(
        $reporte,
        array $todosReportes
    ): array {

        /*
         * Si no existen coordenadas no podemos calcular proximidad.
         */
        if (
            $reporte->latitud === null ||
            $reporte->longitud === null
        ) {
            return [];
        }

        $usuariosCoincidentes = [];

        /*
         * El creador del reporte actual cuenta como el primer vecino.
         */
        $usuariosCoincidentes[
            (string) $reporte->idUsuario
        ] = true;

        foreach ($todosReportes as $otroReporte) {

            /*
             * No comparar el reporte consigo mismo.
             */
            if (
                (int) $otroReporte->idReporte ===
                (int) $reporte->idReporte
            ) {
                continue;
            }

            /*
             * Solo misma categoría.
             */
            if (
                (int) $otroReporte->idCategoria !==
                (int) $reporte->idCategoria
            ) {
                continue;
            }

            /*
             * Reportes solucionados no cuentan.
             */
            if ($this->estaSolucionado($otroReporte->estado)) {
                continue;
            }

            /*
             * Debe tener coordenadas.
             */
            if (
                $otroReporte->latitud === null ||
                $otroReporte->longitud === null
            ) {
                continue;
            }

            /*
             * Calcular distancia en metros.
             */
            $distancia = $this->calcularDistanciaMetros(
                (float) $reporte->latitud,
                (float) $reporte->longitud,
                (float) $otroReporte->latitud,
                (float) $otroReporte->longitud
            );

            /*
             * Radio máximo de coincidencia: 5 metros.
             */
            if ($distancia <= 5) {

                /*
                 * Usamos idUsuario como clave.
                 *
                 * Así, aunque el mismo usuario cree varios reportes
                 * en la misma zona, solamente contará una vez.
                 */
                $usuariosCoincidentes[
                    (string) $otroReporte->idUsuario
                ] = true;
            }
        }

        return array_keys($usuariosCoincidentes);
    }

    /*
    |--------------------------------------------------------------------------
    | NIVEL DE COINCIDENCIA
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
    | COMPROBAR SI UN REPORTE ESTÁ SOLUCIONADO
    |--------------------------------------------------------------------------
    |
    | Aunque ya unificamos la BD en "Solucionado", mantenemos también
    | "Resuelto" temporalmente para que algún dato antiguo no cause errores.
    |
    */
    private function estaSolucionado(
        ?string $estado
    ): bool {

        $estadoNormalizado =
            mb_strtolower(trim($estado ?? ''));

        return in_array(
            $estadoNormalizado,
            [
                'solucionado',
                'resuelto'
            ],
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

        if (count($reportes) == 0) {
            return redirect('/reportes');
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

        if (Session::get('rol') != 'Administrador') {
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
        if (Session::get('rol') != 'Administrador') {
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
            compact(
                'reporte',
                'categorias'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR REPORTE
    |--------------------------------------------------------------------------
    */
    public function actualizar(
        Request $request,
        $id
    ) {
        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        /*
         * Obtener imagen actual.
         */
        $reporte = DB::select("
            SELECT imagen
            FROM reportes
            WHERE idReporte = ?
        ", [$id]);

        if (count($reporte) === 0) {
            return redirect('/reportes');
        }

        $imagen = $reporte[0]->imagen;

        /*
         * Si se subió una nueva imagen.
         */
        if ($request->hasFile('imagen')) {

            /*
             * Eliminar imagen anterior.
             */
            if (
                $imagen &&
                Storage::disk('public')
                    ->exists($imagen)
            ) {
                Storage::disk('public')
                    ->delete($imagen);
            }

            /*
             * Guardar nueva imagen.
             */
            $imagen =
                $request
                    ->file('imagen')
                    ->store(
                        'reportes',
                        'public'
                    );
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
            ],
        ]);

        $ruta = $request
            ->file('imagen')
            ->store(
                'reportes',
                'public'
            );

        return response()->json([
            'success' => true,
            'mensaje' => 'Imagen subida correctamente',
            'ruta' => $ruta,
            'url' => asset(
                'storage/' . $ruta
            ),
        ], 201);
    }
}
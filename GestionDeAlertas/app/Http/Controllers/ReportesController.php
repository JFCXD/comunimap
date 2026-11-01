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
    | LISTADO GENERAL Y CASOS AGRUPADOS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        $esAdministrador =
            Session::get('rol') === 'Administrador';

        // Reportes usados para calcular las coincidencias.
        $todosReportes = DB::select("
            SELECT
                r.idReporte,
                r.titulo,
                r.estado,
                r.idUsuario,
                r.idCategoria,
                r.latitud,
                r.longitud,
                r.direccion,
                c.nombre AS categoria,
                u.nombre AS usuario
            FROM reportes r
            INNER JOIN categorias c
                ON r.idCategoria = c.idCategoria
            INNER JOIN usuarios u
                ON r.idUsuario = u.idUsuario
            ORDER BY r.idReporte DESC
        ");

        // La lista principal respeta los permisos existentes.
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

        if ($esAdministrador) {
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

        // Seguimos calculando coincidencias para cada reporte.
        foreach ($reportes as $reporte) {

            if (
                $this->estaSolucionado($reporte->estado) ||
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

        // Las tablas especiales serán solo administrativas.
        $casosAlta = [];
        $casosMedia = [];
        $casosBaja = [];

        if ($esAdministrador) {

            $grupos = $this->agruparCasos($todosReportes);

            foreach ($grupos as $grupo) {

                if ($grupo['cantidadVecinos'] >= 5) {
                    $casosAlta[] = $grupo;

                } elseif ($grupo['cantidadVecinos'] >= 3) {
                    $casosMedia[] = $grupo;

                } elseif ($grupo['cantidadVecinos'] === 2) {
                    $casosBaja[] = $grupo;
                }
            }

            // Los casos con más vecinos aparecen primero.
            $ordenar = function ($a, $b) {
                return $b['cantidadVecinos']
                    <=> $a['cantidadVecinos'];
            };

            usort($casosAlta, $ordenar);
            usort($casosMedia, $ordenar);
            usort($casosBaja, $ordenar);
        }

        return view(
            'reportes.reportes',
            compact(
                'reportes',
                'casosAlta',
                'casosMedia',
                'casosBaja'
            )
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

        $diferenciaLatitud =
            deg2rad($latitud2 - $latitud1);

        $diferenciaLongitud =
            deg2rad($longitud2 - $longitud1);

        $latitud1Rad = deg2rad($latitud1);
        $latitud2Rad = deg2rad($latitud2);

        $a =
            sin($diferenciaLatitud / 2) ** 2
            +
            cos($latitud1Rad)
            * cos($latitud2Rad)
            * sin($diferenciaLongitud / 2) ** 2;

        $a = min(1, max(0, $a));

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $radioTierra * $c;
    }

    /*
    |--------------------------------------------------------------------------
    | COMPROBAR ESTADO SOLUCIONADO
    |--------------------------------------------------------------------------
    */
    private function estaSolucionado(?string $estado): bool
    {
        $estado = mb_strtolower(trim($estado ?? ''));

        return in_array(
            $estado,
            ['solucionado', 'resuelto'],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | COMPROBAR SI DOS REPORTES COINCIDEN
    |--------------------------------------------------------------------------
    */
    private function sonCoincidentes($primero, $segundo): bool
    {
        if (
            $this->estaSolucionado($primero->estado) ||
            $this->estaSolucionado($segundo->estado)
        ) {
            return false;
        }

        if (
            (int) $primero->idCategoria !==
            (int) $segundo->idCategoria
        ) {
            return false;
        }

        if (
            $primero->latitud === null ||
            $primero->longitud === null ||
            $segundo->latitud === null ||
            $segundo->longitud === null
        ) {
            return false;
        }

        $distancia = $this->calcularDistanciaMetros(
            (float) $primero->latitud,
            (float) $primero->longitud,
            (float) $segundo->latitud,
            (float) $segundo->longitud
        );

        return $distancia <= 5;
    }

    /*
    |--------------------------------------------------------------------------
    | CONTAR USUARIOS DISTINTOS POR REPORTE
    |--------------------------------------------------------------------------
    */
    private function obtenerUsuariosCoincidentes(
        $reporte,
        array $todosReportes
    ): array {
        $usuarios = [
            (string) $reporte->idUsuario => true
        ];

        foreach ($todosReportes as $otro) {

            if (
                (int) $reporte->idReporte ===
                (int) $otro->idReporte
            ) {
                continue;
            }

            if ($this->sonCoincidentes($reporte, $otro)) {
                $usuarios[(string) $otro->idUsuario] = true;
            }
        }

        return array_keys($usuarios);
    }

    /*
    |--------------------------------------------------------------------------
    | NIVEL HISTÓRICO DE COINCIDENCIA
    |--------------------------------------------------------------------------
    | Se conserva el cálculo, aunque ya no mostraremos
    | la columna Nivel en la tabla principal.
    */
    private function obtenerNivelCoincidencia(
        int $cantidad
    ): string {
        if ($cantidad >= 5) {
            return 'Muy reportado';
        }

        if ($cantidad === 4) {
            return 'Frecuente';
        }

        if ($cantidad >= 2) {
            return 'Recurrente';
        }

        return 'Individual';
    }

    /*
    |--------------------------------------------------------------------------
    | AGRUPAR CASOS ESPECIALES
    |--------------------------------------------------------------------------
    |
    | Cada reporte puede pertenecer a un solo grupo.
    | Se usa un reporte representativo como centro.
    | Los demás deben quedar a 5 metros o menos
    | de ese reporte y tener la misma categoría.
    |
    | Se cuentan USUARIOS DISTINTOS.
    */
    private function agruparCasos(array $todosReportes): array
    {
        $activos = [];

        foreach ($todosReportes as $reporte) {

            if ($this->estaSolucionado($reporte->estado)) {
                continue;
            }

            if (
                $reporte->latitud === null ||
                $reporte->longitud === null
            ) {
                continue;
            }

            $activos[] = $reporte;
        }

        $grupos = [];
        $utilizados = [];

        /*
         * Elegimos primero el grupo disponible con más
         * vecinos distintos para dar prioridad a las
         * zonas con mayor cantidad de afectados.
         */
        while (true) {

            $mejorGrupo = [];
            $mejorCantidad = 0;

            foreach ($activos as $centro) {

                if (isset($utilizados[$centro->idReporte])) {
                    continue;
                }

                $candidatos = [];
                $usuarios = [];

                foreach ($activos as $otro) {

                    if (isset($utilizados[$otro->idReporte])) {
                        continue;
                    }

                    if ($this->sonCoincidentes($centro, $otro)) {

                        $candidatos[] = $otro;
                        $usuarios[(string) $otro->idUsuario] =
                            $otro->usuario;
                    }
                }

                $cantidad = count($usuarios);

                if ($cantidad > $mejorCantidad) {

                    $mejorCantidad = $cantidad;

                    $mejorGrupo = [
                        'centro' => $centro,
                        'reportes' => $candidatos,
                        'usuarios' => $usuarios
                    ];
                }
            }

            // No quedan grupos con dos o más vecinos.
            if ($mejorCantidad < 2) {
                break;
            }

            $centro = $mejorGrupo['centro'];
            $integrantes = $mejorGrupo['reportes'];

            foreach ($integrantes as $integrante) {
                $utilizados[$integrante->idReporte] = true;
            }

            $nombres = array_values(
                $mejorGrupo['usuarios']
            );

            $ids = array_map(
                function ($reporte) {
                    return (int) $reporte->idReporte;
                },
                $integrantes
            );

            $grupos[] = [
                'idReporte' => $centro->idReporte,
                'titulo' => $centro->titulo,
                'categoria' => $centro->categoria,
                'direccion' => $centro->direccion,
                'latitud' => $centro->latitud,
                'longitud' => $centro->longitud,
                'cantidadVecinos' => $mejorCantidad,
                'cantidadReportes' => count($integrantes),
                'usuarios' => $nombres,
                'idsReportes' => $ids
            ];
        }

        return $grupos;
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

        if (Session::get('rol') !== 'Administrador') {

            $pertenece = DB::table('reportes')
                ->where('idReporte', $id)
                ->where(
                    'idUsuario',
                    Session::get('idUsuario')
                )
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
        $imagenAnterior = null;

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

        // Borrar la foto anterior solo tras actualizar.
        if (
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

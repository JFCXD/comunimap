<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE REPORTES
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        $esAdministrador =
            Session::get('rol') === 'Administrador';

        $todosReportes = $this->consultarReportes();

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

        // Mantener el cálculo de coincidencias.
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
    | CONSULTAR LOS REPORTES PARA AGRUPACIÓN
    |--------------------------------------------------------------------------
    */
    private function consultarReportes(
        bool $bloquear = false
    ): array {
        $consulta = DB::table('reportes as r')
            ->join(
                'categorias as c',
                'r.idCategoria',
                '=',
                'c.idCategoria'
            )
            ->join(
                'usuarios as u',
                'r.idUsuario',
                '=',
                'u.idUsuario'
            )
            ->select(
                'r.idReporte',
                'r.titulo',
                'r.estado',
                'r.idUsuario',
                'r.idCategoria',
                'r.latitud',
                'r.longitud',
                'r.direccion',
                'c.nombre as categoria',
                'u.nombre as usuario'
            )
            ->orderByDesc('r.idReporte');

        if ($bloquear) {
            $consulta->lockForUpdate();
        }

        return $consulta->get()->all();
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
    | VALIDAR ESTADO SOLUCIONADO
    |--------------------------------------------------------------------------
    */
    private function estaSolucionado(
        ?string $estado
    ): bool {
        $estado = mb_strtolower(
            trim($estado ?? '')
        );

        return in_array(
            $estado,
            ['solucionado', 'resuelto'],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | COMPARAR DOS REPORTES
    |--------------------------------------------------------------------------
    */
    private function sonCoincidentes(
        $primero,
        $segundo
    ): bool {
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
    | CONTAR VECINOS DISTINTOS
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
                $usuarios[
                    (string) $otro->idUsuario
                ] = true;
            }
        }

        return array_keys($usuarios);
    }

    /*
    |--------------------------------------------------------------------------
    | NIVEL DE COINCIDENCIAS
    |--------------------------------------------------------------------------
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
    | Todos los integrantes deben estar a un máximo
    | de 5 metros de un reporte representativo.
    |
    | Se mantiene la agrupación de la etapa anterior
    | para que la clasificación no cambie.
    |
    */
    private function agruparCasos(
        array $todosReportes
    ): array {
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

        while (true) {

            $mejorGrupo = [];
            $mejorCantidad = 0;

            foreach ($activos as $centro) {

                if (
                    isset(
                    $utilizados[
                        $centro->idReporte
                    ]
                )
                ) {
                    continue;
                }

                $candidatos = [];
                $usuarios = [];

                foreach ($activos as $otro) {

                    if (
                        isset(
                        $utilizados[
                            $otro->idReporte
                        ]
                    )
                    ) {
                        continue;
                    }

                    if (
                        $this->sonCoincidentes(
                            $centro,
                            $otro
                        )
                    ) {
                        $candidatos[] = $otro;

                        $usuarios[
                            (string) $otro->idUsuario
                        ] = $otro->usuario;
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

            if ($mejorCantidad < 2) {
                break;
            }

            $centro = $mejorGrupo['centro'];
            $integrantes = $mejorGrupo['reportes'];

            foreach ($integrantes as $integrante) {
                $utilizados[
                    $integrante->idReporte
                ] = true;
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
                'idReporte' => (int) $centro->idReporte,
                'titulo' => $centro->titulo,
                'categoria' => $centro->categoria,
                'direccion' => $centro->direccion,
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
    | ACTUALIZAR ESTADO DE TODOS LOS REPORTES DE UN GRUPO
    |--------------------------------------------------------------------------
    */
    public function actualizarEstadoGrupo(
        Request $request,
        $id
    ) {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') !== 'Administrador') {
            abort(403);
        }

        $datos = $request->validate([
            'estado' => [
                'required',
                'in:Pendiente,En Proceso,Solucionado'
            ]
        ]);

        $nuevoEstado = $datos['estado'];

        // El identificador de la URL es solo el
        // reporte representativo. No aceptamos IDs
        // de otros reportes enviados por el navegador.
        $idRepresentativo = (int) $id;

        $idsNotificaciones = DB::transaction(function () use ($idRepresentativo, $nuevoEstado) {
            $idsNotificaciones = [];

            // Consultar y bloquear los registros
            // mientras se realiza la actualización.
            $todosReportes =
                $this->consultarReportes(true);

            // Volver a calcular los grupos actuales.
            $grupos =
                $this->agruparCasos($todosReportes);

            $grupoSeleccionado = null;

            foreach ($grupos as $grupo) {

                if (
                    (int) $grupo['idReporte'] ===
                    $idRepresentativo
                ) {
                    $grupoSeleccionado = $grupo;
                    break;
                }
            }

            // Si el grupo desapareció o cambió,
            // no debemos modificar reportes equivocados.
            if ($grupoSeleccionado === null) {

                throw ValidationException::withMessages([
                    'grupo' =>
                        'El grupo cambió o ya no existe. '
                        . 'Actualiza la página e inténtalo nuevamente.'
                ]);
            }

            $idsReportes =
                $grupoSeleccionado['idsReportes'];

            if (count($idsReportes) < 2) {

                throw ValidationException::withMessages([
                    'grupo' =>
                        'Este caso ya no tiene suficientes reportes coincidentes.'
                ]);
            }

            // Actualizar todos los integrantes y generar avisos
            // únicamente para aquellos cuyo estado realmente cambió.
            // Los avisos se guardan en la misma transacción.
            DB::table('reportes')
                ->whereIn(
                    'idReporte',
                    $idsReportes
                )
                ->update([
                    'estado' => $nuevoEstado
                ]);

            $idsDelGrupo = array_fill_keys(
                array_map('intval', $idsReportes),
                true
            );

            foreach ($todosReportes as $reporteGrupo) {
                if (isset($idsDelGrupo[(int) $reporteGrupo->idReporte])) {
                    $idNotificacion = $this->registrarNotificacionEstado(
                        $reporteGrupo,
                        $nuevoEstado
                    );
                    if ($idNotificacion !== null) {
                        $idsNotificaciones[] = $idNotificacion;
                    }
                }
            }

            return $idsNotificaciones;
        }, 3);

        $this->enviarPushNotificaciones($idsNotificaciones);

        return redirect('/reportes')
            ->with(
                'success',
                'Se actualizó el estado de todos '
                . 'los reportes del caso agrupado.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE UN REPORTE INDIVIDUAL
    |--------------------------------------------------------------------------
    | Solo el administrador puede modificar este reporte. No altera
    | otros reportes coincidentes ni los casos agrupados.
    */
    public function actualizarEstadoIndividual(Request $request, $id)
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') !== 'Administrador') {
            abort(403);
        }

        $datos = $request->validate([
            'estado' => ['required', 'in:Pendiente,En Proceso,Solucionado'],
        ]);

        $idReporte = (int) $id;
        $nuevoEstado = $datos['estado'];

        $idNotificacion = DB::transaction(function () use ($idReporte, $nuevoEstado) {
            $reporteAnterior = DB::table('reportes')
                ->where('idReporte', $idReporte)
                ->lockForUpdate()
                ->first();

            if (!$reporteAnterior) {
                throw ValidationException::withMessages([
                    'reporte' => 'Este reporte ya no existe. Actualiza la página.',
                ]);
            }

            if (trim((string) $reporteAnterior->estado) === $nuevoEstado) {
                return null;
            }

            DB::table('reportes')
                ->where('idReporte', $idReporte)
                ->update(['estado' => $nuevoEstado]);

            return $this->registrarNotificacionEstado($reporteAnterior, $nuevoEstado);
        }, 3);

        if ($idNotificacion !== null) {
            $this->enviarPushNotificaciones([$idNotificacion]);
        }

        return redirect('/reportes')
            ->with('success', 'Estado del reporte #' . $idReporte . ' actualizado a ' . $nuevoEstado . '.');
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFICACIONES INTERNAS POR CAMBIO DE ESTADO
    |--------------------------------------------------------------------------
    | Reutiliza la tabla de notificaciones de ComuniMap.
    | Se ejecuta dentro de la transacción que modifica el reporte.
    | No notifica cuando el estado ya era el mismo ni al volver a Pendiente.
    */
    private function registrarNotificacionEstado(
        $reporteAnterior,
        string $nuevoEstado
    ): ?int {
        $estadoAnterior = trim(
            (string) ($reporteAnterior->estado ?? '')
        );

        if ($estadoAnterior === $nuevoEstado) {
            return null;
        }

        if ($nuevoEstado === 'En Proceso') {
            $titulo = 'Tu reporte está siendo atendido';
            $mensaje = 'El reporte "' .
                (string) $reporteAnterior->titulo .
                '" cambió a En Proceso.';
            $tipo = 'EN_PROCESO';
        } elseif ($nuevoEstado === 'Solucionado') {
            $titulo = 'Tu reporte fue solucionado';
            $mensaje = 'El reporte "' .
                (string) $reporteAnterior->titulo .
                '" fue marcado como Solucionado.';
            $tipo = 'SOLUCIONADO';
        } else {
            return null;
        }

        return (int) DB::table('notificaciones')->insertGetId([
            'idUsuario' => (int) $reporteAnterior->idUsuario,
            'idReporte' => (int) $reporteAnterior->idReporte,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'leida' => 0
        ], 'idNotificacion');
    }

    /*
    |--------------------------------------------------------------------------
    | ENVIAR PUSH DESPUÉS DEL COMMIT
    |--------------------------------------------------------------------------
    | La API valida una clave privada independiente y lee los
    | destinatarios desde las notificaciones ya guardadas.
    | Nunca se reenvían actualizaciones de estados a la API.
    */
    private function enviarPushNotificaciones(array $ids): void
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) {
            return;
        }

        $secreto = (string) config('services.comunimap.web_push_secret', '');
        if (strlen($secreto) < 32) {
            \Log::warning('ComuniMap: falta COMUNIMAP_WEB_PUSH_SECRET en Laravel');
            return;
        }

        $url = 'https://comunimap-api-production.up.railway.app/push_notificaciones_web.php';

        foreach (array_chunk($ids, 50) as $lote) {
            try {
                $respuesta = \Illuminate\Support\Facades\Http::timeout(25)
                    ->acceptJson()
                    ->withHeaders([
                        'X-Comunimap-Web-Push-Secret' => $secreto,
                    ])
                    ->post($url, [
                        'idsNotificaciones' => $lote
                    ]);

                if (!$respuesta->successful() ||
                    $respuesta->json('success') !== true) {
                    \Log::warning('ComuniMap: el envío push fue rechazado', [
                        'status' => $respuesta->status(),
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('ComuniMap: no se pudo contactar la API push', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PDF INDIVIDUAL
    |--------------------------------------------------------------------------
    */
    /**
     * Consulta de un reporte, sin editarlo ni modificar su estado.
     * Un vecino solamente puede consultar sus propios reportes.
     */
    public function ver($id)
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        $consulta = DB::table('reportes as r')
            ->join('categorias as c', 'r.idCategoria', '=', 'c.idCategoria')
            ->join('usuarios as u', 'r.idUsuario', '=', 'u.idUsuario')
            ->where('r.idReporte', $id)
            ->select('r.idReporte', 'r.titulo', 'r.descripcion', 'r.ubicacion',
                'r.direccion', 'r.estado', 'r.imagen', 'r.fechaCreacion',
                'r.idUsuario', 'c.nombre as categoria', 'u.nombre as usuario');

        if (Session::get('rol') !== 'Administrador') {
            $consulta->where('r.idUsuario', Session::get('idUsuario'));
        }

        $reporte = $consulta->first();
        if (!$reporte) {
            abort(404);
        }

        return view('reportes.ver', compact('reporte'));
    }

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
    | ACTUALIZAR REPORTE INDIVIDUAL
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

        // Validar todos los campos antes de subir una fotografía o alterar datos.
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'min:3', 'max:150'],
            'descripcion' => ['required', 'string', 'min:10', 'max:3000'],
            'direccion' => ['required', 'string', 'min:3', 'max:255'],
            'idCategoria' => ['required', 'integer', 'exists:categorias,idCategoria'],
            'estado' => ['required', 'in:Pendiente,En Proceso,Solucionado'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'idCategoria.exists' => 'Selecciona una categoría válida.',
            'imagen.image' => 'La evidencia debe ser una imagen válida.',
            'imagen.max' => 'La imagen no debe superar 5 MB.',
        ]);

        $imagen = $reporte[0]->imagen;
        $imagenAnterior = null;
        $nuevaImagen = null;

        if ($request->hasFile('imagen')) {
            $nuevaImagen = $request->file('imagen')->store('reportes', 'public');
            $imagenAnterior = $imagen;
            $imagen = $nuevaImagen;
        }

        try {
            $idNotificacion = DB::transaction(function () use ($datos, $id, $imagen) {
            $reporteAnterior = DB::table('reportes')
                ->where('idReporte', $id)
                ->lockForUpdate()
                ->first();

            if (!$reporteAnterior) {
                throw ValidationException::withMessages([
                    'reporte' => 'El reporte ya no existe.'
                ]);
            }

            DB::table('reportes')
                ->where('idReporte', $id)
                ->update([
                    'titulo' => trim($datos['titulo']),
                    'descripcion' => trim($datos['descripcion']),
                    'direccion' => trim($datos['direccion']),
                    'estado' => $datos['estado'],
                    'idCategoria' => (int) $datos['idCategoria'],
                    'imagen' => $imagen
                ]);

            return $this->registrarNotificacionEstado(
                $reporteAnterior,
                $datos['estado']
            );
            }, 3);
        } catch (\Throwable $e) {
            // No conservar archivos huérfanos si falla la actualización en MySQL.
            if ($nuevaImagen !== null) {
                Storage::disk('public')->delete($nuevaImagen);
            }
            throw $e;
        }

        if ($idNotificacion !== null) {
            $this->enviarPushNotificaciones([$idNotificacion]);
        }

        if (
            $imagenAnterior &&
            Storage::disk('public')->exists(
                $imagenAnterior
            )
        ) {
            Storage::disk('public')->delete(
                $imagenAnterior
            );
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

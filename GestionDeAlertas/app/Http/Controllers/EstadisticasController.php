<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') !== 'Administrador') {
            return redirect('/inicio');
        }

        $filtros = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:desde'],
            'categoria' => ['nullable', 'integer', 'min:1'],
            'estado' => ['nullable', 'in:Pendiente,En Proceso,Solucionado'],
        ]);

        $desde = $filtros['desde'] ?? null;
        $hasta = $filtros['hasta'] ?? null;
        $categoriaSeleccionada = $filtros['categoria'] ?? null;
        $estadoSeleccionado = $filtros['estado'] ?? null;

        $categoriasDisponibles = DB::table('categorias')
            ->select('idCategoria', 'nombre')
            ->orderBy('nombre')
            ->get();

        $consulta = function () use ($desde, $hasta, $categoriaSeleccionada, $estadoSeleccionado) {
            return DB::table('reportes as r')
                ->when($desde, fn ($q) => $q->where('r.fechaCreacion', '>=', $desde . ' 00:00:00'))
                ->when($hasta, fn ($q) => $q->where('r.fechaCreacion', '<=', $hasta . ' 23:59:59'))
                ->when($categoriaSeleccionada, fn ($q) => $q->where('r.idCategoria', $categoriaSeleccionada))
                ->when($estadoSeleccionado, fn ($q) => $q->where('r.estado', $estadoSeleccionado));
        };

        $totalReportes = (clone $consulta())->count();
        $totalUsuarios = DB::table('usuarios')->count();

        $estados = (clone $consulta())
            ->select('r.estado', DB::raw('COUNT(*) as total'))
            ->groupBy('r.estado')
            ->pluck('total', 'estado');

        $pendientes = (int) ($estados['Pendiente'] ?? 0);
        $enProceso = (int) ($estados['En Proceso'] ?? 0);
        $solucionados = (int) ($estados['Solucionado'] ?? 0);
        $otrosEstados = max(0, $totalReportes - $pendientes - $enProceso - $solucionados);
        $porcentajeResolucion = $totalReportes ? round($solucionados * 100 / $totalReportes, 1) : 0;

        $categorias = DB::table('categorias as c')
            ->leftJoinSub(
                (clone $consulta())
                    ->select('r.idCategoria', DB::raw('COUNT(*) as total'))
                    ->groupBy('r.idCategoria'),
                'conteos',
                'conteos.idCategoria',
                '=',
                'c.idCategoria'
            )
            ->select('c.nombre as categoria', DB::raw('COALESCE(conteos.total, 0) as total'))
            ->when($categoriaSeleccionada, fn ($q) => $q->where('c.idCategoria', $categoriaSeleccionada))
            ->orderByDesc('total')
            ->orderBy('c.nombre')
            ->get();

        // Vista temporal de prueba: evolución diaria del mes actual.
        // Los filtros generales siguen aplicándose a esta consulta.
        $inicioMes = Carbon::now()->startOfMonth();
        $finHoy = Carbon::now()->endOfDay();
        $conteosDiarios = (clone $consulta())
            ->whereBetween('r.fechaCreacion', [$inicioMes->toDateTimeString(), $finHoy->toDateTimeString()])
            ->selectRaw("DATE(r.fechaCreacion) as periodo, COUNT(*) as total")
            ->groupBy('periodo')
            ->orderBy('periodo')
            ->pluck('total', 'periodo');

        $evolucion = collect();
        for ($dia = $inicioMes->copy(); $dia->lte($finHoy); $dia->addDay()) {
            $fecha = $dia->toDateString();
            $evolucion->push([
                'periodo' => $dia->format('d/m'),
                'total' => (int) ($conteosDiarios[$fecha] ?? 0),
            ]);
        }
        $ultimoReporte = (clone $consulta())->max('r.fechaCreacion');

        return view('estadisticas.estadisticas', [
            'totalUsuarios' => $totalUsuarios,
            'totalReportes' => $totalReportes,
            'pendientes' => $pendientes,
            'enProceso' => $enProceso,
            'solucionados' => $solucionados,
            'otrosEstados' => $otrosEstados,
            'porcentajeResolucion' => $porcentajeResolucion,
            'categorias' => $categorias,
            'categoriasDisponibles' => $categoriasDisponibles,
            'evolucion' => $evolucion,
            'ultimoReporte' => $ultimoReporte,
            'desde' => $desde,
            'hasta' => $hasta,
            'categoriaSeleccionada' => $categoriaSeleccionada,
            'estadoSeleccionado' => $estadoSeleccionado,
        ]);
    }
}

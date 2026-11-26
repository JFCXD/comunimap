<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * Fase 1: consultas de solo lectura para dos apartados independientes.
 * No modifica reportes, documentos, API ni esquema de base de datos.
 */
class ModulosSeguimientoController extends Controller
{
    private function soloAdministrador()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }
        if (Session::get('rol') !== 'Administrador') {
            abort(403, 'Este apartado es exclusivo de administración.');
        }
        return null;
    }

    private function listado(Request $request, bool $solucionados)
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'categoria' => ['nullable', 'integer', 'exists:categorias,idCategoria'],
        ]);

        $query = DB::table('reportes as r')
            ->join('categorias as c', 'c.idCategoria', '=', 'r.idCategoria')
            ->join('usuarios as u', 'u.idUsuario', '=', 'r.idUsuario')
            ->select(
                'r.idReporte', 'r.titulo', 'r.descripcion', 'r.estado',
                'r.imagen', 'r.direccion', 'r.ubicacion', 'r.fechaCreacion',
                'r.idCategoria', 'c.nombre as categoria',
                'u.nombre as usuario', 'u.apellido as apellido'
            );

        if ($solucionados) {
            $query->whereRaw("LOWER(TRIM(r.estado)) IN ('solucionado', 'resuelto')");
        } else {
            $query->whereRaw("LOWER(TRIM(r.estado)) NOT IN ('solucionado', 'resuelto')");
        }

        if (isset($filtros['categoria'])) {
            $query->where('r.idCategoria', $filtros['categoria']);
        }
        if (!empty($filtros['buscar'])) {
            $buscar = mb_strtolower(trim($filtros['buscar']));
            $query->where(function ($q) use ($buscar) {
                $q->whereRaw('LOWER(r.titulo) LIKE ?', ['%'.$buscar.'%'])
                  ->orWhereRaw('LOWER(u.nombre) LIKE ?', ['%'.$buscar.'%'])
                  ->orWhere('r.idReporte', 'like', '%'.$buscar.'%');
            });
        }

        return [
            'registros' => $query->orderByDesc('r.idReporte')->limit(250)->get(),
            'categorias' => DB::table('categorias')->orderBy('nombre')->get(['idCategoria', 'nombre']),
            'filtros' => $filtros,
        ];
    }

    public function cartas(Request $request)
    {
        if ($respuesta = $this->soloAdministrador()) return $respuesta;

        $data = $this->listado($request, false);
        $data['documentos'] = DB::table('cartas_documentos')
            ->select('id','codigo','estado','asunto','reportes_json','creado_por','created_at','updated_at','emitida_en')
            ->orderByDesc('updated_at')->limit(200)->get();
        return view('seguimiento.cartas', $data);
    }

    public function solucionados(Request $request)
    {
        if ($respuesta = $this->soloAdministrador()) return $respuesta;

        return view('seguimiento.solucionados', $this->listado($request, true));
    }
}

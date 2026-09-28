<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EstadisticasController extends Controller
{
    public function index()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        // TOTAL DE USUARIOS
        $usuarios = DB::select("
            SELECT COUNT(*) AS total
            FROM usuarios
        ");

        // TOTAL DE REPORTES
        $reportes = DB::select("
            SELECT COUNT(*) AS total
            FROM reportes
        ");

        // PENDIENTES
        $pendientes = DB::select("
            SELECT COUNT(*) AS total
            FROM reportes
            WHERE estado='Pendiente'
        ");

        // EN PROCESO
        $proceso = DB::select("
            SELECT COUNT(*) AS total
            FROM reportes
            WHERE estado='En Proceso'
        ");

        // SOLUCIONADOS
        $solucionados = DB::select("
            SELECT COUNT(*) AS total
            FROM reportes
            WHERE estado='Solucionado'
        ");

        // REPORTES POR CATEGORÍA
        $categorias = DB::select("
            SELECT
                c.nombre AS categoria,
                COUNT(r.idReporte) AS total
            FROM categorias c
            LEFT JOIN reportes r
                ON r.idCategoria = c.idCategoria
            GROUP BY c.idCategoria, c.nombre
            ORDER BY total DESC
        ");

        return view('estadisticas.estadisticas', compact(
            'usuarios',
            'reportes',
            'pendientes',
            'proceso',
            'solucionados',
            'categorias'
        ));
    }
}
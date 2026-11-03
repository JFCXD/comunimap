<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InicioController extends Controller
{
    public function index()
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
");

        // Mostrar en Inicio únicamente la publicación más reciente que ya sea pública.
        $ultimaPublicacion = DB::table('publicaciones')
            ->where('estado', 'Publicado')
            ->orderByDesc('fechaCreacion')
            ->orderByDesc('idPublicacion')
            ->first();

        return view('inicio.index', compact('reportes', 'ultimaPublicacion'));
    }

}

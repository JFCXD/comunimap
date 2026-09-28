<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
class ReportesController extends Controller
{
    public function index()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') == 'Administrador') {

            $reportes = DB::select("
            SELECT
                r.idReporte,
                r.titulo,
                r.imagen,
                c.nombre,
                r.estado,
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
                c.nombre,
                r.estado,
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

        return view('reportes.reportes', compact('reportes'));
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

        if (count($reportes) == 0) {
            return redirect('/reportes');
        }

        $pdf = Pdf::loadView('reportes.pdf', compact('reportes'));

        return $pdf->stream('reporte-' . $id . '.pdf');
    }

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

        $pdf = Pdf::loadView('reportes.pdfTodos', compact('reportes'));

        return $pdf->stream('Listado_Reportes.pdf');
    }

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

        return view('reportes.editarr', compact('reporte', 'categorias'));
    }
    
    public function actualizar(Request $request, $id)
    {
        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        // Obtener la imagen actual
        $reporte = DB::select("
        SELECT imagen
        FROM reportes
        WHERE idReporte = ?
    ", [$id]);

        $imagen = $reporte[0]->imagen;

        // Si se subió una nueva imagen
        if ($request->hasFile('imagen')) {

            // Eliminar la imagen anterior
            if ($imagen && Storage::disk('public')->exists($imagen)) {
                Storage::disk('public')->delete($imagen);
            }

            // Guardar la nueva imagen
            $imagen = $request->file('imagen')->store('reportes', 'public');
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
}
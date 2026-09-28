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
        $rutaImagen = null;

        // Verificar si se seleccionó una imagen
        if ($request->hasFile('imagen')) {

            $imagen = $request->file('imagen');

            // Guardar imagen en storage/app/public/imagenes
            $rutaImagen = $imagen->store('imagenes', 'public');
        }

        DB::insert("
        INSERT INTO reportes
        (
            titulo,
            descripcion,
            ubicacion,
            direccion,
            estado,
            idUsuario,
            idCategoria,
            imagen
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?
        )
    ", [
            $request->titulo,
            $request->descripcion,
            $request->ubicacion,
            $request->direccion,
            'Pendiente',
            Session::get('idUsuario'),
            $request->idCategoria,
            $rutaImagen
        ]);

        return redirect('/alertas');
    }
}

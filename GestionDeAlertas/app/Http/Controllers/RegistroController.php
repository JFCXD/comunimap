<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroController extends Controller
{
    public function index()
    {
        return view('registro.registro');
    }
    public function guardar(Request $request)
    {
        DB::insert("
        INSERT INTO usuarios
        (nombre, email, password, telefono, rol, estado)
        VALUES (?, ?, ?, ?, ?, ?)
    ", [
            $request->nombre,
            $request->email,
            sha1($request->password),
            $request->telefono,
            'Usuario',
            'Activo'
        ]);

        return redirect('/login');
    }
}

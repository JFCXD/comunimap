<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegistroController extends Controller
{
    public function index()
    {
        return view('registro.registro');
    }

    public function guardar(Request $request)
    {
        // =====================================================
        // VALIDACIÓN
        // =====================================================

        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'password' => 'required',
            'confirmar' => 'required|same:password',
            'telefono' => 'nullable|string|max:20',
        ]);

        // =====================================================
        // VERIFICAR CORREO
        // =====================================================

        $existe = DB::table('usuarios')
            ->where('email', $request->email)
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe una cuenta registrada con este correo.');
        }

        // =====================================================
        // DEFINIR ESTADO
        // =====================================================

        // Si un administrador crea el usuario desde el panel:
        // queda activo inmediatamente.
        //
        // Si es registro público:
        // queda pendiente de aprobación.

        $estado = Session::get('rol') === 'Administrador'
            ? 'Activo'
            : 'Inactivo';

        // =====================================================
        // INSERTAR
        // =====================================================

        DB::insert("
            INSERT INTO usuarios
            (
                nombre,
                email,
                password,
                telefono,
                rol,
                estado
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ", [
            trim($request->nombre),
            trim($request->email),
            sha1($request->password),
            $request->telefono,
            'Usuario',
            $estado
        ]);

        // =====================================================
        // RESPUESTA
        // =====================================================

        if (Session::get('rol') === 'Administrador') {
            return redirect('/usuarios')
                ->with('success', 'Usuario creado correctamente.');
        }

        return redirect('/login')
            ->with(
                'success',
                'Cuenta creada correctamente. Tu cuenta está pendiente de aprobación por un administrador.'
            );
    }
}
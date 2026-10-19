<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UsuariosController extends Controller
{
    public function index()
    {
        return view('usuarios.usuarios');
    }

    public function mostrarUsuarios()
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        // Mostrar primero los usuarios pendientes.
        $usuarios = DB::select("
            SELECT *
            FROM usuarios
            ORDER BY
                CASE
                    WHEN estado = 'Inactivo' THEN 0
                    ELSE 1
                END,
                idUsuario DESC
        ");

        return view(
            'usuarios.usuarios',
            compact('usuarios')
        );
    }

    public function editar($id)
    {
        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        $usuario = DB::select("
            SELECT *
            FROM usuarios
            WHERE idUsuario = ?
        ", [$id]);

        return view(
            'usuarios.editaru',
            compact('usuario')
        );
    }

    public function actualizar(Request $request, $id)
    {
        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        DB::update("
            UPDATE usuarios
            SET
                nombre = ?,
                email = ?,
                telefono = ?,
                rol = ?,
                estado = ?
            WHERE idUsuario = ?
        ", [
            $request->nombre,
            $request->email,
            $request->telefono,
            $request->rol,
            $request->estado,
            $id
        ]);

        return redirect('/usuarios')
            ->with(
                'success',
                'Usuario actualizado correctamente.'
            );
    }

    // =====================================================
    // ACTIVAR USUARIO
    // =====================================================

    public function activar($id)
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        DB::update("
            UPDATE usuarios
            SET estado = 'Activo'
            WHERE idUsuario = ?
        ", [$id]);

        return redirect('/usuarios')
            ->with(
                'success',
                'Usuario aprobado y activado correctamente.'
            );
    }

    public function eliminar($id)
    {
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }

        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        DB::delete("
            DELETE FROM usuarios
            WHERE idUsuario = ?
        ", [$id]);

        return redirect('/usuarios')
            ->with(
                'success',
                'Usuario eliminado correctamente.'
            );
    }
}
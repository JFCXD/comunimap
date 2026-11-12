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
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }
        if (Session::get('rol') !== 'Administrador') {
            return redirect('/inicio');
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:150'],
            'fechaNacimiento' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'email' => ['required', 'email', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'rol' => ['required', 'in:Administrador,Usuario'],
            'estado' => ['required', 'in:Activo,Inactivo'],
        ]);

        $actualizacion = [
            'nombre' => trim($datos['nombre']),
            'apellido' => isset($datos['apellido']) ? trim($datos['apellido']) : null,
            'fechaNacimiento' => $datos['fechaNacimiento'] ?? null,
            'email' => trim($datos['email']),
            'telefono' => isset($datos['telefono']) ? trim($datos['telefono']) : null,
            'rol' => $datos['rol'],
            'estado' => $datos['estado'],
        ];

        DB::table('usuarios')->where('idUsuario', $id)->update($actualizacion);

        return redirect('/usuarios')
            ->with('success', 'Usuario actualizado correctamente.');
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
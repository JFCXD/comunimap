<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

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
        if (!Session::has('idUsuario')) {
            return redirect('/login');
        }
        if (Session::get('rol') != 'Administrador') {
            return redirect('/inicio');
        }

        $usuario = DB::select("
            SELECT *
            FROM usuarios
            WHERE idUsuario = ?
        ", [$id]);

        if (count($usuario) === 0) {
            abort(404);
        }

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

        abort_unless(ctype_digit((string) $id) && (int) $id > 0, 404);
        abort_unless(DB::table('usuarios')->where('idUsuario', $id)->exists(), 404);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:150'],
            'fechaNacimiento' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'email' => ['required', 'email', 'max:100', Rule::unique('usuarios', 'email')->ignore((int) $id, 'idUsuario')],
            'telefono' => ['required', 'string', 'regex:/^\\+?[0-9]{7,15}$/', 'max:20'],
            'rol' => ['required', 'in:Administrador,Usuario'],
            'estado' => ['required', 'in:Activo,Inactivo'],
        ], [
            'email.unique' => 'Este correo electrónico ya pertenece a otro usuario.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
            'telefono.regex' => 'El teléfono debe tener entre 7 y 15 dígitos; puede comenzar con +.',
        ]);

        $actualizacion = [
            'nombre' => trim($datos['nombre']),
            'apellido' => isset($datos['apellido']) ? trim($datos['apellido']) : null,
            'fechaNacimiento' => $datos['fechaNacimiento'] ?? null,
            'email' => trim($datos['email']),
            'telefono' => trim($datos['telefono']),
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

        abort_unless(ctype_digit((string) $id) && (int) $id > 0, 404);
        abort_unless(DB::table('usuarios')->where('idUsuario', $id)->exists(), 404);

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

        abort_unless(ctype_digit((string) $id) && (int) $id > 0, 404);
        abort_unless(DB::table('usuarios')->where('idUsuario', $id)->exists(), 404);

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
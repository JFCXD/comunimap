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
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:100'],
            'apellido' => ['required', 'string', 'max:150'],
            'fechaNacimiento' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'email' => ['required', 'email', 'max:190'],
            'telefono' => ['required', 'string', 'regex:/^\\+?[0-9]{7,15}$/', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'max:72'],
            'confirmar' => ['required', 'same:password'],
        ], [
            'nombre.required' => 'Ingresa tu nombre.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'apellido.required' => 'Ingresa tu apellido.',
            'fechaNacimiento.required' => 'Ingresa tu fecha de nacimiento.',
            'fechaNacimiento.before_or_equal' => 'La fecha de nacimiento no puede estar en el futuro.',
            'telefono.required' => 'Ingresa un número de teléfono de contacto.',
            'telefono.regex' => 'Ingresa un teléfono válido: de 7 a 15 dígitos, con + opcional al inicio.',
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'password.required' => 'Ingresa una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'confirmar.required' => 'Confirma tu contraseña.',
            'confirmar.same' => 'Las contraseñas no coinciden.',
        ]);

        $correo = trim($datos['email']);

        if (DB::table('usuarios')->where('email', $correo)->exists()) {
            return back()->withInput($request->except('password', 'confirmar'))
                ->withErrors(['email' => 'Ya existe una cuenta registrada con este correo.']);
        }

        // Mantener la compatibilidad con el inicio de sesión actual (SHA-1).
        // La migración de contraseñas se realizará posteriormente.
        try {
            DB::table('usuarios')->insert([
                'nombre' => trim($datos['nombre']),
                'apellido' => trim($datos['apellido']),
                'fechaNacimiento' => $datos['fechaNacimiento'],
                'email' => $correo,
                'telefono' => trim($datos['telefono']),
                'password' => sha1($datos['password']),
                'rol' => 'Usuario',
                'estado' => 'Inactivo',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::warning('ComuniMap: error al registrar cuenta', [
                'codigo' => $e->getCode(),
            ]);
            return back()->withInput($request->except('password', 'confirmar'))
                ->with('error', 'No se pudo completar el registro. Comprueba los datos e inténtalo nuevamente.');
        }

        return redirect('/login')->with(
            'success',
            'Cuenta registrada correctamente. Está pendiente de aprobación por el administrador.'
        );
    }
}

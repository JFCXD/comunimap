<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.login');
    }

    public function ingresar(Request $request)
    {
        // =====================================================
        // VERIFICAR reCAPTCHA
        // =====================================================

        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );

        $resultado = $response->json();

        if (
            !isset($resultado['success']) ||
            !$resultado['success']
        ) {
            return redirect('/login')
                ->withInput($request->only('email'))
                ->with('error', 'Debe verificar el reCAPTCHA.');
        }

        // =====================================================
        // BUSCAR USUARIO
        // =====================================================

        // Buscamos primero por correo y contraseña.
        // No filtramos por estado para poder informar
        // si la cuenta está pendiente.

        $usuario = DB::select("
            SELECT *
            FROM usuarios
            WHERE email = ?
              AND password = ?
            LIMIT 1
        ", [
            trim($request->email),
            sha1($request->password)
        ]);

        // =====================================================
        // CREDENCIALES INCORRECTAS
        // =====================================================

        if (count($usuario) === 0) {
            return redirect('/login')
                ->withInput($request->only('email'))
                ->with('error', 'Correo o contraseña incorrectos.');
        }

        $usuarioActual = $usuario[0];

        // =====================================================
        // VALIDAR ESTADO
        // =====================================================

        if (
            strtolower(trim($usuarioActual->estado)) !== 'activo'
        ) {
            return redirect('/login')
                ->withInput($request->only('email'))
                ->with(
                    'error',
                    'Tu cuenta está pendiente de aprobación por un administrador.'
                );
        }

        // =====================================================
        // INICIAR SESIÓN
        // =====================================================

        Session::put(
            'idUsuario',
            $usuarioActual->idUsuario
        );

        Session::put(
            'nombre',
            $usuarioActual->nombre
        );

        Session::put(
            'rol',
            $usuarioActual->rol
        );

        return redirect('/inicio');
    }

    public function salir()
    {
        Session::flush();

        return redirect('/login');
    }
}
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
        // Verificar reCAPTCHA
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );
        $resultado = $response->json();
        if (!$resultado['success']) {
            return redirect('/login')
                ->with('error', 'Debe verificar el reCAPTCHA.');
        }

        $usuario = DB::select("
        SELECT * FROM usuarios
        WHERE email = ?
        AND password = ?
        AND estado = ?
    ", [
            $request->email,
            sha1($request->password),
            'Activo'
        ]);

        if (count($usuario) > 0) {

            Session::put('idUsuario', $usuario[0]->idUsuario);
            Session::put('nombre', $usuario[0]->nombre);
            Session::put('rol', $usuario[0]->rol);

            return redirect('/inicio');
        }

        return redirect('/login')->with('error', 'Correo o contraseña incorrectos.');
    }

    public function salir()
    {
        Session::flush(); // Elimina todas las variables de sesión

        return redirect('/login');
    }
}

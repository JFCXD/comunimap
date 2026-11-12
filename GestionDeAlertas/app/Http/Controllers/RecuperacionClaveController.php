<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class RecuperacionClaveController extends Controller
{
    private const MINUTOS = 15;
    private const MENSAJE = 'Si la cuenta existe y tiene un correo registrado, recibirás instrucciones para restablecer el acceso.';

    public function solicitarFormulario()
    {
        return view('login.recuperar');
    }

    public function solicitar(Request $request)
    {
        $datos = $request->validate(['email' => ['required', 'email', 'max:190']]);
        $correo = trim($datos['email']);
        $usuario = DB::table('usuarios')->where('email', $correo)->first(['idUsuario', 'email']);

        // Evitar diferencias visibles que permitan conocer quién tiene cuenta.
        if (!$usuario) {
            return back()->with('success', self::MENSAJE);
        }

        $correoHabilitado = !in_array(config('mail.default'), ['log', 'array'], true)
            && filter_var(config('mail.from.address'), FILTER_VALIDATE_EMAIL)
            && config('mail.from.address') !== 'hello@example.com';

        if (!$correoHabilitado) {
            Log::warning('Recuperación ComuniMap: transporte de correo no configurado');
            return back()->with('success', self::MENSAJE);
        }

        $token = bin2hex(random_bytes(32));

        // Un solo código activo por usuario; impedir peticiones constantes incluso con IP variable.
        $creado = DB::transaction(function () use ($usuario, $token) {
            $anterior = DB::table('recuperaciones_clave')
                ->where('idUsuario', $usuario->idUsuario)->lockForUpdate()->first();
            if ($anterior && now()->diffInSeconds($anterior->created_at, false) > -60) {
                return false;
            }
            DB::table('recuperaciones_clave')->updateOrInsert(
                ['idUsuario' => $usuario->idUsuario],
                [
                    'token_hash' => hash('sha256', $token),
                    'metodo' => 'correo',
                    'administrador_id' => null,
                    'vence_en' => DB::raw('DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE)'),
                    'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                    'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
                ]
            );
            return true;
        });

        if (!$creado) {
            return back()->with('success', self::MENSAJE);
        }

        $enlace = route('recuperacion.formulario', [
            'id' => $usuario->idUsuario,
            'token' => $token,
        ]);
        try {
            Mail::raw(
                "Se solicitó restablecer la contraseña de ComuniMap.\n\n".
                "Abre el siguiente enlace en los próximos 15 minutos:\n".$enlace."\n\n".
                "El enlace solo puede utilizarse una vez. Si no lo solicitaste, ignora este mensaje.",
                function ($mensaje) use ($usuario) {
                    $mensaje->to($usuario->email)->subject('ComuniMap - Recuperación de contraseña');
                }
            );
        } catch (\Throwable $e) {
            DB::table('recuperaciones_clave')->where('idUsuario', $usuario->idUsuario)
                ->where('token_hash', hash('sha256', $token))->delete();
            Log::warning('ComuniMap: el proveedor de correo rechazó una recuperación');
        }

        return back()->with('success', self::MENSAJE);
    }

    public function formulario(int $id, string $token)
    {
        if (!$this->valido($id, $token, 'correo')) {
            return redirect()->route('recuperacion.solicitar')
                ->with('error', 'El enlace es inválido o ha caducado. Solicita uno nuevo.');
        }
        return response()->view('login.nueva-clave', [
            'id' => $id,
            'token' => $token,
            'via' => 'correo',
        ])->header('Cache-Control', 'no-store, private')
          ->header('Referrer-Policy', 'no-referrer');
    }

    public function codigoFormulario()
    {
        return view('login.codigo-recuperacion');
    }

    public function codigoVerificar(Request $request)
    {
        $datos = $request->validate([
            'idUsuario' => ['required', 'integer', 'min:1'],
            'codigo' => ['required', 'string', 'max:40'],
        ]);
        $codigo = strtoupper(preg_replace('/[\s-]+/', '', $datos['codigo']));
        $usuario = DB::table('usuarios')->where('idUsuario', $datos['idUsuario'])
            ->where('rol', 'Usuario')->first(['idUsuario']);
        if (!$usuario || !$this->valido((int) $usuario->idUsuario, $codigo, 'administrador')) {
            return back()->withInput($request->only('idUsuario'))
                ->withErrors(['codigo' => 'El código no es válido o ha caducado.']);
        }

        return response()->view('login.nueva-clave', [
            'id' => (int) $usuario->idUsuario,
            'token' => $codigo,
            'via' => 'administrador',
        ])->header('Cache-Control', 'no-store, private')
          ->header('Referrer-Policy', 'no-referrer');
    }

    public function cambiar(Request $request)
    {
        $datos = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'token' => ['required', 'string', 'min:20', 'max:128'],
            'via' => ['required', 'in:correo,administrador'],
            'password' => ['required', 'string', 'min:6', 'max:10', 'confirmed'],
        ]);
        $token = $datos['via'] === 'administrador'
            ? strtoupper(preg_replace('/[\s-]+/', '', $datos['token']))
            : $datos['token'];

        $actualizado = DB::transaction(function () use ($datos, $token) {
            $registro = DB::table('recuperaciones_clave')
                ->where('idUsuario', $datos['id'])->lockForUpdate()->first();
            if (!$registro || $registro->metodo !== $datos['via']
                || !hash_equals($registro->token_hash, hash('sha256', $token))
                || !DB::table('recuperaciones_clave')->where('idUsuario', $datos['id'])
                    ->where('vence_en', '>=', DB::raw('CURRENT_TIMESTAMP'))->exists()) {
                return false;
            }

            // Compatibilidad TEMPORAL con inicio de sesión web y API PHP Android.
            // No cambiar a bcrypt sin actualizar simultáneamente ambas autenticaciones.
            $afectados = DB::table('usuarios')->where('idUsuario', $datos['id'])
                ->update(['password' => sha1($datos['password'])]);
            if ($afectados < 1) return false;
            DB::table('recuperaciones_clave')->where('idUsuario', $datos['id'])->delete();
            return true;
        });

        if (!$actualizado) {
            return redirect()->route('recuperacion.solicitar')
                ->with('error', 'El enlace o código no es válido o ha caducado.');
        }

        // No iniciar sesión de forma automática ni cambiar el estado de aprobación.
        return redirect('/login')->with('success',
            'Contraseña actualizada. Inicia sesión con tu nueva contraseña.');
    }

    public function emitirCodigo(Request $request, int $id)
    {
        abort_unless(Session::has('idUsuario') && Session::get('rol') === 'Administrador', 403);
        $request->validate([
            'identidad_verificada' => ['accepted'],
        ], ['identidad_verificada.accepted' =>
            'Debes verificar personalmente la identidad del vecino antes de emitir el código.']);

        $usuario = DB::table('usuarios')->where('idUsuario', $id)->first(['idUsuario', 'rol']);
        abort_unless($usuario && $usuario->rol === 'Usuario', 404);

        // 24 caracteres aleatorios: no se guarda el código en texto plano.
        $codigo = strtoupper(bin2hex(random_bytes(12)));
        DB::table('recuperaciones_clave')->updateOrInsert(
            ['idUsuario' => $usuario->idUsuario],
            [
                'token_hash' => hash('sha256', $codigo),
                'metodo' => 'administrador',
                'administrador_id' => Session::get('idUsuario'),
                'vence_en' => now()->addMinutes(self::MINUTOS),
                'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
            ]
        );

        return redirect('/usuarios')
            ->with('success', 'Código generado. Entrégalo únicamente al vecino tras verificar su identidad.')
            ->with('codigo_recuperacion', implode('-', str_split($codigo, 6)))
            ->with('usuario_recuperacion', $id);
    }

    private function valido(int $id, string $token, string $via): bool
    {
        $registro = DB::table('recuperaciones_clave')->where('idUsuario', $id)
            ->where('metodo', $via)
            ->where('vence_en', '>=', DB::raw('CURRENT_TIMESTAMP'))->first();
        return $registro && hash_equals($registro->token_hash, hash('sha256', $token));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ComunidadController extends Controller
{
    private function usuario()
    {
        if (!Session::has('idUsuario')) {
            return null;
        }
        return DB::table('usuarios')->where('idUsuario', Session::get('idUsuario'))
            ->where('estado', 'Activo')->first();
    }

    private function administrador($usuario): bool
    {
        return $usuario && $usuario->rol === 'Administrador';
    }

    public function index(Request $request)
    {
        $usuario = $this->usuario();
        if (!$usuario) return redirect('/login');

        $admin = $this->administrador($usuario);
        $publicaciones = DB::table('publicaciones as p')
            ->leftJoin('usuarios as u', 'u.idUsuario', '=', 'p.idAutor')
            ->select('p.*', 'u.nombre as autor')
            ->when(!$admin, fn($q) => $q->where('p.estado', 'Publicado'))
            ->orderByDesc('p.fechaCreacion')->orderByDesc('p.idPublicacion')
            ->paginate(10);

        $ids = $publicaciones->getCollection()->pluck('idPublicacion')->all();
        $opciones = collect();
        $misVotos = collect();
        if ($ids) {
            $opciones = DB::table('opciones_encuesta as o')
                ->leftJoin('votos_encuesta as v', 'v.idOpcion', '=', 'o.idOpcion')
                ->select('o.idPublicacion', 'o.idOpcion', 'o.texto', 'o.orden',
                    DB::raw('COUNT(v.idVoto) as total'))
                ->whereIn('o.idPublicacion', $ids)
                ->groupBy('o.idPublicacion', 'o.idOpcion', 'o.texto', 'o.orden')
                ->orderBy('o.orden')->orderBy('o.idOpcion')->get()->groupBy('idPublicacion');

            $misVotos = DB::table('votos_encuesta')->where('idUsuario', $usuario->idUsuario)
                ->whereIn('idPublicacion', $ids)->pluck('idOpcion', 'idPublicacion');
        }

        return view('comunidad.comunidad', compact('publicaciones', 'opciones', 'misVotos', 'admin'));
    }

    public function guardar(Request $request)
    {
        $usuario = $this->usuario();
        if (!$usuario) return redirect('/login');
        if (!$this->administrador($usuario)) abort(403);

        $datos = $request->validate([
            'tipo' => ['required', Rule::in(['Comunicado', 'Actividad', 'Encuesta'])],
            'titulo' => ['required', 'string', 'max:180'],
            'descripcion' => ['required', 'string', 'max:10000'],
            'estado' => ['required', Rule::in(['Borrador', 'Publicado'])],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'fechaActividad' => ['nullable', 'required_if:tipo,Actividad', 'date'],
            'horaActividad' => ['nullable', 'required_if:tipo,Actividad', 'date_format:H:i'],
            'lugarActividad' => ['nullable', 'required_if:tipo,Actividad', 'string', 'max:255'],
            'fechaCierre' => ['nullable', 'required_if:tipo,Encuesta', 'date', 'after:now'],
            'opciones' => ['nullable', 'required_if:tipo,Encuesta', 'array', 'min:2', 'max:10'],
            'opciones.*' => ['required_if:tipo,Encuesta', 'string', 'max:255'],
        ]);

        $tipo = $datos['tipo'];
        $respuestas = $tipo === 'Encuesta'
            ? array_values(array_filter(array_map('trim', $datos['opciones'] ?? []), fn($v) => $v !== ''))
            : [];
        if ($tipo === 'Encuesta' && (count($respuestas) < 2 || count(array_unique(array_map('mb_strtolower', $respuestas))) !== count($respuestas))) {
            throw ValidationException::withMessages(['opciones' => 'Ingresa al menos dos opciones distintas y sin espacios vacíos.']);
        }

        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombre = bin2hex(random_bytes(16)).'.'.$archivo->extension();
            $carpeta = public_path('uploads/comunidad');
            if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true) && !is_dir($carpeta)) {
                throw ValidationException::withMessages(['imagen' => 'No se pudo preparar el almacenamiento de imágenes.']);
            }
            $archivo->move($carpeta, $nombre);
            $rutaImagen = 'uploads/comunidad/'.$nombre;
        }

        try {
            DB::transaction(function () use ($datos, $usuario, $tipo, $respuestas, $rutaImagen) {
                $id = DB::table('publicaciones')->insertGetId([
                    'idAutor' => $usuario->idUsuario,
                    'tipo' => $tipo,
                    'titulo' => trim($datos['titulo']),
                    'descripcion' => trim($datos['descripcion']),
                    'imagen' => $rutaImagen,
                    'fechaActividad' => $tipo === 'Actividad' ? ($datos['fechaActividad'] ?? null) : null,
                    'horaActividad' => $tipo === 'Actividad' ? ($datos['horaActividad'] ?? null) : null,
                    'lugarActividad' => $tipo === 'Actividad' ? trim($datos['lugarActividad']) : null,
                    'fechaCierre' => $tipo === 'Encuesta' ? $datos['fechaCierre'] : null,
                    'estado' => $datos['estado'],
                ]);

                foreach ($respuestas as $indice => $respuesta) {
                    DB::table('opciones_encuesta')->insert([
                        'idPublicacion' => $id,
                        'texto' => $respuesta,
                        'orden' => $indice + 1,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            if ($rutaImagen && file_exists(public_path($rutaImagen))) @unlink(public_path($rutaImagen));
            throw $e;
        }

        return redirect('/comunidad')->with('success', 'Publicación guardada correctamente.');
    }

    public function actualizar(Request $request, $id)
    {
        $usuario = $this->usuario();
        if (!$usuario) return redirect('/login');
        if (!$this->administrador($usuario)) abort(403);

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'descripcion' => ['required', 'string', 'max:10000'],
            'fechaActividad' => ['nullable', 'date'],
            'horaActividad' => ['nullable', 'date_format:H:i'],
            'lugarActividad' => ['nullable', 'string', 'max:255'],
            'fechaCierre' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($id, $datos) {
            $pub = DB::table('publicaciones')->where('idPublicacion', $id)->lockForUpdate()->first();
            if (!$pub) abort(404);

            // Mantener la pregunta y el cierre intactos una vez registrada la primera respuesta.
            if ($pub->tipo === 'Encuesta' && DB::table('votos_encuesta')->where('idPublicacion', $id)->exists()) {
                throw ValidationException::withMessages(['titulo' => 'Esta encuesta ya recibió votos y no puede editarse. Puedes archivarla si es necesario.']);
            }

            $campos = [
                'titulo' => trim($datos['titulo']),
                'descripcion' => trim($datos['descripcion']),
            ];

            if ($pub->tipo === 'Actividad') {
                foreach (['fechaActividad', 'horaActividad', 'lugarActividad'] as $campo) {
                    if (empty($datos[$campo])) {
                        throw ValidationException::withMessages([$campo => 'Completa la información de la actividad.']);
                    }
                    $campos[$campo] = $datos[$campo];
                }
            }
            if ($pub->tipo === 'Encuesta') {
                // Las preguntas y opciones pueden corregirse antes de la votación,
                // pero nunca se alteran después de registrar votos.
                if (!empty($datos['fechaCierre'])) {
                    if (DB::table('votos_encuesta')->where('idPublicacion', $id)->exists()) {
                        throw ValidationException::withMessages(['fechaCierre' => 'No se puede modificar el cierre después de recibir votos.']);
                    }
                    if (\Carbon\Carbon::parse($datos['fechaCierre'])->isPast()) {
                        throw ValidationException::withMessages(['fechaCierre' => 'La fecha de cierre debe ser futura.']);
                    }
                    $campos['fechaCierre'] = $datos['fechaCierre'];
                }
            }
            DB::table('publicaciones')->where('idPublicacion', $id)->update($campos);
        });

        return redirect('/comunidad#publicacion-'.$id)->with('success', 'Publicación editada correctamente.');
    }

    public function estado(Request $request, $id)
    {
        $usuario = $this->usuario();
        if (!$usuario) return redirect('/login');
        if (!$this->administrador($usuario)) abort(403);

        $datos = $request->validate(['estado' => ['required', Rule::in(['Borrador','Publicado','Archivado'])]]);
        $publicacion = DB::table('publicaciones')->where('idPublicacion', $id)->first();
        if (!$publicacion) abort(404);
        if ($publicacion->tipo === 'Encuesta' && $datos['estado'] === 'Publicado') {
            $n = DB::table('opciones_encuesta')->where('idPublicacion', $id)->count();
            if ($n < 2 || !$publicacion->fechaCierre) {
                return back()->withErrors(['estado' => 'La encuesta necesita opciones y una fecha de cierre.']);
            }
        }
        DB::table('publicaciones')->where('idPublicacion', $id)->update(['estado' => $datos['estado']]);
        return back()->with('success', 'Estado de la publicación actualizado.');
    }

    public function votar(Request $request, $id)
    {
        $usuario = $this->usuario();
        if (!$usuario) return redirect('/login');
        $datos = $request->validate(['idOpcion' => ['required', 'integer']]);

        try {
            DB::transaction(function () use ($id, $datos, $usuario) {
                $publicacion = DB::table('publicaciones')->where('idPublicacion', $id)
                    ->lockForUpdate()->first();
                if (!$publicacion || $publicacion->tipo !== 'Encuesta' || $publicacion->estado !== 'Publicado') {
                    throw ValidationException::withMessages(['voto' => 'Esta encuesta no está disponible.']);
                }
                if (!$publicacion->fechaCierre || now()->greaterThanOrEqualTo(\Carbon\Carbon::parse($publicacion->fechaCierre))) {
                    throw ValidationException::withMessages(['voto' => 'Esta encuesta ya finalizó.']);
                }
                $opcion = DB::table('opciones_encuesta')
                    ->where('idPublicacion', $id)->where('idOpcion', $datos['idOpcion'])->exists();
                if (!$opcion) throw ValidationException::withMessages(['voto' => 'La opción no pertenece a esta encuesta.']);
                if (DB::table('votos_encuesta')->where('idPublicacion', $id)
                    ->where('idUsuario', $usuario->idUsuario)->exists()) {
                    throw ValidationException::withMessages(['voto' => 'Ya registraste tu voto en esta encuesta.']);
                }
                DB::table('votos_encuesta')->insert([
                    'idPublicacion' => $id,
                    'idOpcion' => $datos['idOpcion'],
                    'idUsuario' => $usuario->idUsuario,
                ]);
            }, 3);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->withErrors(['voto' => 'Ya registraste tu voto en esta encuesta.']);
        }
        return back()->with('success', 'Tu voto quedó registrado.');
    }
}

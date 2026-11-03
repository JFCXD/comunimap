<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DocumentoFormalController extends Controller
{
    private function administrador(): void
    {
        if (!Session::has('idUsuario') || Session::get('rol') !== 'Administrador') {
            abort(403);
        }
    }

    private function ids($ids): array
    {
        if (!is_array($ids) || count($ids) === 0 || count($ids) > 30) {
            throw ValidationException::withMessages([
                'reportes' => 'Selecciona entre 1 y 30 reportes para la carta.',
            ]);
        }

        $resultado = [];
        foreach ($ids as $id) {
            if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id <= 0) {
                throw ValidationException::withMessages(['reportes' => 'Hay un reporte no válido.']);
            }
            $resultado[] = (int) $id;
        }

        $unicos = array_values(array_unique($resultado));
        if (count($unicos) !== count($resultado)) {
            throw ValidationException::withMessages(['reportes' => 'No selecciones el mismo reporte dos veces.']);
        }
        return $unicos;
    }

    private function reportes(array $ids)
    {
        $reportes = DB::table('reportes as r')
            ->join('categorias as c', 'c.idCategoria', '=', 'r.idCategoria')
            ->whereIn('r.idReporte', $ids)
            ->select('r.idReporte', 'r.titulo', 'r.descripcion', 'r.direccion',
                'r.ubicacion', 'r.estado', 'r.fechaCreacion', 'r.imagen',
                'c.nombre as categoria')
            ->orderBy('r.idReporte')
            ->get();

        if ($reportes->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'reportes' => 'Alguno de los reportes seleccionados ya no existe.',
            ]);
        }

        return $reportes;
    }

    public function preparar(Request $request)
    {
        $this->administrador();
        $ids = $this->ids($request->input('reportes', []));
        $reportes = $this->reportes($ids);

        $antecedentes = $reportes->map(function ($reporte) {
            $ubicacion = trim((string) ($reporte->direccion ?: $reporte->ubicacion ?: ''));
            $texto = 'Reporte #'.$reporte->idReporte.' — '.$reporte->titulo.
                ' ('.$reporte->categoria.')';
            if ($ubicacion !== '') $texto .= ', ubicación: '.$ubicacion;
            $texto .= '.';
            if (trim((string) $reporte->descripcion) !== '') {
                $texto .= ' '.trim((string) $reporte->descripcion);
            }
            return $texto;
        })->implode("\n\n");

        return view('documentos.formulario', [
            'reportes' => $reportes,
            'antecedentes' => $antecedentes,
            'ids' => $ids,
        ]);
    }

    public function descargar(Request $request)
    {
        $this->administrador();

        $datos = $request->validate([
            'reportes' => ['required', 'array', 'min:1', 'max:30'],
            'reportes.*' => ['required'],
            'fecha' => ['required', 'date_format:Y-m-d'],
            'destinatario' => ['required', 'string', 'max:180'],
            'cargo_destinatario' => ['nullable', 'string', 'max:180'],
            'institucion' => ['required', 'string', 'max:180'],
            'asunto' => ['required', 'string', 'max:250'],
            'remitente' => ['required', 'string', 'max:180'],
            'cargo_remitente' => ['nullable', 'string', 'max:180'],
            'antecedentes' => ['required', 'string', 'max:15000'],
            'solicitud' => ['required', 'string', 'max:10000'],
            'incluir_anexos' => ['nullable', 'in:1'],
            'modo' => ['nullable', 'in:vista_previa,descargar'],
        ]);

        $ids = $this->ids($datos['reportes']);
        $reportes = $this->reportes($ids);

        $fotos = [];
        if ($request->boolean('incluir_anexos')) {
            foreach ($reportes as $reporte) {
                $ruta = $reporte->imagen;
                if (!is_string($ruta) || !preg_match('~^reportes/[a-zA-Z0-9_.-]+\.(jpg|jpeg|png|webp)$~iD', $ruta)) {
                    continue;
                }

                // Leer SOLO del disco público de reportes; jamás confiar en rutas absolutas.
                if (!Storage::disk('public')->exists($ruta)) continue;
                $contenido = Storage::disk('public')->get($ruta);
                if (!is_string($contenido) || strlen($contenido) > 6 * 1024 * 1024) continue;
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contenido);
                if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
                    // DomPDF no maneja todos los formatos WEBP de forma portable.
                    continue;
                }
                $fotos[] = [
                    'idReporte' => $reporte->idReporte,
                    'titulo' => $reporte->titulo,
                    'imagen' => 'data:'.$mime.';base64,'.base64_encode($contenido),
                ];
            }
        }

        $pdf = Pdf::loadView('documentos.pdf', [
            'datos' => $datos,
            'reportes' => $reportes,
            'fotos' => $fotos,
        ])->setPaper('a4');

        $nombre = 'carta-formal-comunimap-'.date('Ymd').'.pdf';
        return ($datos['modo'] ?? 'descargar') === 'vista_previa'
            ? $pdf->stream($nombre)
            : $pdf->download($nombre);
    }
}

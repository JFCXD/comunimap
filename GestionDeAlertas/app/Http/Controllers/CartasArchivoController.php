<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartasArchivoController extends Controller
{
    private function administrador(): void
    {
        if (!Session::has('idUsuario') || Session::get('rol') !== 'Administrador') {
            abort(403);
        }
    }

    private function reportesValidos($ids): array
    {
        if (!is_array($ids) || count($ids) < 1 || count($ids) > 30) {
            throw ValidationException::withMessages(['reportes' => 'Selecciona entre 1 y 30 reportes.']);
        }
        $resultado = [];
        foreach ($ids as $id) {
            if (!is_scalar($id) || !ctype_digit((string) $id) || (int)$id < 1) {
                throw ValidationException::withMessages(['reportes' => 'Identificador de reporte inválido.']);
            }
            $resultado[] = (int)$id;
        }
        if (count(array_unique($resultado)) !== count($resultado)
            || DB::table('reportes')->whereIn('idReporte', $resultado)->count() !== count($resultado)) {
            throw ValidationException::withMessages(['reportes' => 'Uno de los reportes no existe o está repetido.']);
        }
        return $resultado;
    }

    public function guardarBorrador(Request $request)
    {
        $this->administrador();
        $request->validate([
            'documento_id' => ['nullable', 'integer', 'min:1'],
            'reportes' => ['required', 'array', 'min:1', 'max:30'],
            'reportes.*' => ['required'],
            'asunto' => ['nullable', 'string', 'max:250'],
            'fecha' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $ids = $this->reportesValidos($request->input('reportes'));
        $datos = $request->except(['_token', '_method', 'modo', 'accion_documento']);
        $datos['reportes'] = $ids;
        $datos['incluir_anexos'] = $request->boolean('incluir_anexos') ? '1' : null;

        // Solo campos que conoce el editor. Los campos HTML se limpian con el
        // mismo filtro utilizado para crear el PDF: nunca se almacena HTML libre.
        $campos = [
            'fecha'=>10,'lugar_emision'=>120,'institucion'=>180,'destinatario'=>180,
            'cargo_destinatario'=>180,'asunto'=>250,'antecedentes'=>15000,
            'solicitud'=>10000,'remitente'=>180,'cargo_remitente'=>180,
            'membrete_titulo'=>250,'membrete_prefectural'=>250,'membrete_municipal'=>250,
            'membrete_registro'=>250,'membrete_zona'=>250,'tratamiento'=>250,
            'presente'=>250,'referencia_etiqueta'=>250,'saludo'=>250,'despedida'=>250,
            'atentamente'=>250,'firma_otb'=>250,'sello_etiqueta'=>250,'pie_carta'=>250,
        ];
        $limpios = ['reportes'=>$ids, 'incluir_anexos'=>$datos['incluir_anexos']];
        $formato = app(DocumentoFormalController::class);
        foreach ($campos as $nombre=>$max) {
            $valor = $request->input($nombre);
            if ($valor !== null && !is_string($valor)) {
                throw ValidationException::withMessages([$nombre=>'Campo inválido.']);
            }
            if ($valor !== null && mb_strlen($valor) > $max) {
                throw ValidationException::withMessages([$nombre=>'Texto demasiado largo.']);
            }
            $limpios[$nombre] = $valor ?? '';
            if ($nombre !== 'fecha' && $nombre !== 'lugar_emision' && $nombre !== 'institucion'
                && $nombre !== 'destinatario' && $nombre !== 'cargo_destinatario'
                && $nombre !== 'asunto' && $nombre !== 'remitente' && $nombre !== 'cargo_remitente'
                && $request->filled($nombre.'_html')) {
                $limpios[$nombre.'_html'] = $formato->formatoSeguroCarta($request->input($nombre.'_html'));
            }
        }

        $idDocumento = $request->input('documento_id');
        $ahora = now();
        $payload = [
            'asunto' => mb_substr(trim((string)($limpios['asunto'] ?? '')), 0, 250),
            'datos_json' => json_encode($limpios, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE),
            'reportes_json' => json_encode($ids),
            'updated_at' => $ahora,
        ];

        if ($idDocumento) {
            $documento = DB::table('cartas_documentos')->where('id', (int)$idDocumento)
                ->where('estado', 'Borrador')->first();
            if (!$documento) abort(404);
            DB::table('cartas_documentos')->where('id', $documento->id)
                ->where('estado', 'Borrador')->update($payload);
            $id = $documento->id;
        } else {
            $id = DB::table('cartas_documentos')->insertGetId($payload + [
                'estado'=>'Borrador',
                'creado_por'=>(int) Session::get('idUsuario'),
                'created_at'=>$ahora,
            ]);
        }
        return redirect()->route('cartas.editar', ['id'=>$id])
            ->with('success', 'Borrador guardado. Puedes continuar editándolo cuando quieras.');
    }

    public function editar(int $id)
    {
        $this->administrador();
        $documento = DB::table('cartas_documentos')->where('id', $id)
            ->where('estado', 'Borrador')->first();
        if (!$documento) abort(404);
        $datos = json_decode($documento->datos_json, true);
        if (!is_array($datos)) abort(422, 'El contenido del borrador no es válido.');
        $datos['reportes'] = $this->reportesValidos($datos['reportes'] ?? []);
        $documentoId = (int)$documento->id;
        return view('documentos.editor', compact('datos', 'documentoId'));
    }

    public function pdf(int $id)
    {
        $this->administrador();
        $documento = DB::table('cartas_documentos')->where('id', $id)
            ->where('estado', 'Emitida')->first();
        if (!$documento || !$documento->pdf_binario) abort(404);
        $nombre = ($documento->codigo ?: 'carta-'.$documento->id).'.pdf';
        return response($documento->pdf_binario, 200, [
            'Content-Type'=>'application/pdf',
            'Content-Disposition'=>'inline; filename="'.$nombre.'"',
            'Cache-Control'=>'private, no-store, max-age=0',
            'X-Content-Type-Options'=>'nosniff',
        ]);
    }

    public function eliminarBorrador(int $id)
    {
        $this->administrador();
        $eliminados = DB::table('cartas_documentos')->where('id', $id)
            ->where('estado', 'Borrador')->delete();
        if (!$eliminados) abort(404);
        return redirect()->route('cartas.index')->with('success', 'Borrador eliminado.');
    }
}

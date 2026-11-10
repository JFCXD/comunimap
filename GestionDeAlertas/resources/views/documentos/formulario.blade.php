@include('plantillas.encabezado')
<body>
<div class="preloader"><div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div></div>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
     data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
@include('plantillas.encabezado2')
@include('plantillas.menu')
<div class="page-wrapper">

<div class="container py-4" style="max-width:1000px">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Preparar carta formal</h2>
            <p class="text-muted mb-0">{{ count($ids) === 1 ? 'Documento individual' : 'Documento conjunto' }} — {{ count($ids) }} reporte(s)</p>
        </div>
        <a href="{{ url('/reportes') }}" class="btn btn-outline-secondary">Volver a reportes</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Revisa los datos:</strong>
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5>Reportes seleccionados</h5>
            <ul class="mb-0">
                @foreach($reportes as $reporte)
                    <li><strong>#{{ $reporte->idReporte }} — {{ $reporte->titulo }}</strong> ({{ $reporte->categoria }})</li>
                @endforeach
            </ul>
            <small class="text-muted">El documento no cambia los reportes ni sus estados.</small>
        </div>
    </div>

    <form id="cmFormularioCarta" action="{{ route('documentos.descargar') }}" method="POST">
        @csrf
        @foreach($ids as $id)
            <input type="hidden" name="reportes[]" value="{{ $id }}">
        @endforeach

        <div class="card mb-4" id="cmSeccionIA">
            <div class="card-body">
                <h5 class="mb-2"><i class="fa-solid fa-wand-magic-sparkles me-1" aria-hidden="true"></i> Propuesta de carta con Gemini (opcional)</h5>
                <p class="text-muted small">La IA redacta un borrador a partir de los reportes elegidos. No cambia los reportes ni escribe en los campos hasta que aceptes la propuesta.</p>
                <div class="mb-2">
                    <label for="cmIndicacionesIA" class="form-label">Indicaciones adicionales (opcional)</label>
                    <textarea id="cmIndicacionesIA" class="form-control" maxlength="500" rows="2" placeholder="Ej.: solicitar inspección técnica y una respuesta por escrito"></textarea>
                </div>
                <button type="button" id="cmGenerarIA" class="btn btn-outline-primary">
                    <i class="fa-solid fa-wand-magic-sparkles me-1" aria-hidden="true"></i> Generar borrador con IA
                </button>
                <div id="cmEstadoIA" class="small mt-2" role="status" aria-live="polite"></div>
                <div id="cmVistaIA" class="border rounded p-3 mt-3 d-none">
                    <h6 class="mb-2">Propuesta generada — revisa antes de aceptar</h6>
                    <p class="mb-1"><strong>Asunto</strong></p>
                    <p id="cmPropuestaAsunto" style="white-space:pre-wrap;overflow-wrap:anywhere;"></p>
                    <p class="mb-1"><strong>Antecedentes</strong></p>
                    <p id="cmPropuestaAntecedentes" style="white-space:pre-wrap;overflow-wrap:anywhere;"></p>
                    <p class="mb-1"><strong>Solicitud</strong></p>
                    <p id="cmPropuestaSolicitud" style="white-space:pre-wrap;overflow-wrap:anywhere;"></p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" id="cmAceptarIA" class="btn btn-primary btn-sm">Aceptar borrador</button>
                        <button type="button" id="cmDescartarIA" class="btn btn-outline-secondary btn-sm">Descartar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3">Datos de la carta</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="fecha">Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="institucion">Empresa o institución</label>
                        <input class="form-control" id="institucion" name="institucion" maxlength="180" value="{{ old('institucion') }}" placeholder="Ej.: empresa de servicios o entidad municipal" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="destinatario">Destinatario</label>
                        <input class="form-control" id="destinatario" name="destinatario" maxlength="180" value="{{ old('destinatario') }}" placeholder="Nombre o autoridad competente" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cargo_destinatario">Cargo del destinatario (opcional)</label>
                        <input class="form-control" id="cargo_destinatario" name="cargo_destinatario" maxlength="180" value="{{ old('cargo_destinatario') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="asunto">Asunto</label>
                        <input class="form-control" id="asunto" name="asunto" maxlength="250" value="{{ old('asunto', count($ids) === 1 ? 'Solicitud de atención a reporte comunitario' : 'Solicitud de atención a reportes comunitarios') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="antecedentes">Antecedentes y descripción de los hechos (editable)</label>
                        <textarea class="form-control" id="antecedentes" name="antecedentes" rows="8" maxlength="15000" required>{{ old('antecedentes', $antecedentes) }}</textarea>
                        <small class="text-muted">Texto inicial tomado de los reportes. Revisa la redacción y confirma los hechos antes de generar la carta.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="solicitud">Solicitud formal</label>
                        <textarea class="form-control" id="solicitud" name="solicitud" rows="5" maxlength="10000" required>{{ old('solicitud', 'Por medio de la presente, solicitamos respetuosamente la evaluación y atención de los problemas descritos, así como una respuesta sobre las acciones que correspondan.') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="remitente">Nombre de quien firma</label>
                        <input class="form-control" id="remitente" name="remitente" maxlength="180" value="{{ old('remitente') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cargo_remitente">Cargo o representación (opcional)</label>
                        <input class="form-control" id="cargo_remitente" name="cargo_remitente" maxlength="180" value="{{ old('cargo_remitente', 'Representante de la OTB') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5>Fotografías del documento</h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="incluir_anexos" value="1" id="incluir_anexos" {{ old('incluir_anexos') ? 'checked' : '' }}>
                    <label class="form-check-label" for="incluir_anexos">Incluir fotografías de los reportes como anexos al final del PDF</label>
                </div>
                <p class="text-muted small mb-0 mt-2">Si no marcas esta opción, se genera únicamente la carta. Los reportes sin fotografía no bloquean la descarga. La primera versión incorpora anexos JPG y PNG; las fotografías WEBP necesitan una conversión compatible antes de incluirse.</p>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" name="modo" value="vista_previa" formtarget="_blank" class="btn btn-outline-primary"><i class="fa fa-eye me-1"></i> Vista previa PDF</button>
            <button type="submit" name="modo" value="descargar" class="btn btn-primary"><i class="fa fa-file-pdf-o me-1"></i> Descargar PDF formal</button>
            <a href="{{ url('/reportes') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>


@include('plantillas.pie')
</div>
</div>
@include('plantillas.scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const generar = document.getElementById('cmGenerarIA');
    const estado = document.getElementById('cmEstadoIA');
    const vista = document.getElementById('cmVistaIA');
    const aceptar = document.getElementById('cmAceptarIA');
    const descartar = document.getElementById('cmDescartarIA');
    const campos = ['asunto', 'antecedentes', 'solicitud'];
    let propuesta = null;
    let generando = false;

    function estadoMensaje(mensaje, error) {
        estado.textContent = mensaje;
        estado.className = 'small mt-2 ' + (error ? 'text-danger' : 'text-muted');
    }
    function limpiar() {
        propuesta = null;
        vista.classList.add('d-none');
    }
    descartar.addEventListener('click', function () {
        limpiar();
        estadoMensaje('Propuesta descartada. Tu carta no se ha modificado.', false);
    });
    aceptar.addEventListener('click', function () {
        if (!propuesta) return;
        // Solo ahora se sustituyen los tres campos editables.
        campos.forEach(function (nombre) {
            document.getElementById(nombre).value = propuesta[nombre];
        });
        limpiar();
        estadoMensaje('Borrador incorporado. Revisa y edita el texto antes de generar el PDF.', false);
        document.getElementById('asunto').focus();
    });
    generar.addEventListener('click', async function () {
        if (generando) return;
        const institucion = document.getElementById('institucion').value.trim();
        if (!institucion) {
            estadoMensaje('Primero escribe la empresa o institución destinataria.', true);
            document.getElementById('institucion').focus();
            return;
        }
        const formulario = document.getElementById('cmFormularioCarta');
        const ids = Array.from(formulario.querySelectorAll('input[name="reportes[]"]')).map(e => e.value);
        if (ids.length < 1 || ids.length > 15) {
            estadoMensaje('La IA admite entre 1 y 15 reportes por borrador; la carta manual admite hasta 30.', true);
            return;
        }
        const cuerpo = new FormData();
        ids.forEach(id => cuerpo.append('reportes[]', id));
        cuerpo.append('institucion', institucion);
        cuerpo.append('destinatario', document.getElementById('destinatario').value);
        cuerpo.append('instrucciones', document.getElementById('cmIndicacionesIA').value);
        cuerpo.append('_token', formulario.querySelector('input[name="_token"]').value);

        generar.disabled = true;
        generando = true;
        limpiar();
        estadoMensaje('Generando propuesta con Gemini…', false);
        try {
            const respuesta = await fetch(@json(route('documentos.borrador-ia')), {
                method: 'POST',
                body: cuerpo,
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const datos = await respuesta.json();
            if (!respuesta.ok || !datos.success) {
                throw new Error(datos.message || 'No se pudo generar el borrador.');
            }
            if (!datos.borrador || !campos.every(campo => typeof datos.borrador[campo] === 'string')) {
                throw new Error('La propuesta recibida no tiene el formato esperado.');
            }
            propuesta = datos.borrador;
            document.getElementById('cmPropuestaAsunto').textContent = propuesta.asunto;
            document.getElementById('cmPropuestaAntecedentes').textContent = propuesta.antecedentes;
            document.getElementById('cmPropuestaSolicitud').textContent = propuesta.solicitud;
            vista.classList.remove('d-none');
            estadoMensaje('Borrador listo. Acepta o descarta después de revisarlo.', false);
        } catch (error) {
            estadoMensaje(error.message || 'No se pudo conectar con Gemini.', true);
        } finally {
            generando = false;
            generar.disabled = false;
        }
    });
});
</script>
</body>
</html>

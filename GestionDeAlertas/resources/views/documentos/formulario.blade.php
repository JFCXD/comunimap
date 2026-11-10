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
                    {{-- Gemini integrado en la misma sección Datos de la carta; no crea una segunda tarjeta. --}}
                    <div class="col-12" id="cmSeccionIA">
                        <div class="d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap">
                            <button type="button" id="cmGenerarIA" class="btn btn-outline-primary btn-sm flex-shrink-0"
                                    title="Generar una propuesta editable de asunto, antecedentes y solicitud">
                                <i class="fa-solid fa-wand-magic-sparkles me-1" aria-hidden="true"></i>
                                Generar borrador con IA
                            </button>
                            <label for="cmIndicacionesIA" class="visually-hidden">Indicaciones adicionales para Gemini (opcional)</label>
                            <input type="text" id="cmIndicacionesIA" class="form-control form-control-sm flex-grow-1"
                                   style="min-width:180px;" maxlength="500"
                                   placeholder="Indicaciones adicionales (opcional): ej. redactar breve y cordial"
                                   aria-label="Indicaciones adicionales para el borrador (opcional)">
                        </div>
                        <div id="cmEstadoIA" class="small mt-2" role="status" aria-live="polite"></div>
                        <button type="button" id="cmDeshacerIA" class="btn btn-link btn-sm px-0 mt-1 d-none">
                            <i class="fa-solid fa-rotate-left me-1" aria-hidden="true"></i>
                            Deshacer borrador de IA
                        </button>
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
    const deshacer = document.getElementById('cmDeshacerIA');
    const campos = ['asunto', 'antecedentes', 'solicitud'];
    let anterior = null;
    let aplicado = null;
    let generando = false;

    function avisar(mensaje, error) {
        estado.textContent = mensaje;
        estado.className = 'small mt-2 ' + (error ? 'text-danger' : 'text-success');
    }
    function valoresActuales() {
        const valores = {};
        campos.forEach(function (nombre) {
            valores[nombre] = document.getElementById(nombre).value;
        });
        return valores;
    }
    function escribir(valores) {
        campos.forEach(function (nombre) {
            document.getElementById(nombre).value = valores[nombre];
        });
    }
    function coinciden(a, b) {
        return campos.every(function (nombre) { return a[nombre] === b[nombre]; });
    }
    deshacer.addEventListener('click', function () {
        if (anterior === null) return;
        // No borrar cambios manuales escritos después de utilizar la IA.
        if (!coinciden(valoresActuales(), aplicado)) {
            avisar('Has editado la carta después de generar el borrador. No se deshizo para evitar perder tus cambios.', true);
            deshacer.classList.add('d-none');
            anterior = null;
            aplicado = null;
            return;
        }
        escribir(anterior);
        anterior = null;
        aplicado = null;
        deshacer.classList.add('d-none');
        avisar('Se recuperaron los textos anteriores. Puedes seguir editando la carta.', false);
    });
    generar.addEventListener('click', async function () {
        if (generando) return;
        const institucion = document.getElementById('institucion').value.trim();
        if (!institucion) {
            avisar('Primero escribe la empresa o institución destinataria.', true);
            document.getElementById('institucion').focus();
            return;
        }
        const formulario = document.getElementById('cmFormularioCarta');
        const ids = Array.from(formulario.querySelectorAll('input[name="reportes[]"]')).map(e => e.value);
        if (ids.length < 1 || ids.length > 15) {
            avisar('La IA admite entre 1 y 15 reportes por borrador; la carta manual admite hasta 30.', true);
            return;
        }
        const cuerpo = new FormData();
        ids.forEach(id => cuerpo.append('reportes[]', id));
        cuerpo.append('institucion', institucion);
        cuerpo.append('destinatario', document.getElementById('destinatario').value);
        cuerpo.append('instrucciones', document.getElementById('cmIndicacionesIA').value);
        cuerpo.append('_token', formulario.querySelector('input[name="_token"]').value);
        const textosAntesDeEnviar = valoresActuales();

        generar.disabled = true;
        generando = true;
        avisar('Gemini está redactando los campos de la carta…', false);
        try {
            const respuesta = await fetch(@json(url('/documentos/borrador-ia')), {
                method: 'POST',
                body: cuerpo,
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const datos = await respuesta.json();
            if (!respuesta.ok || !datos.success) {
                throw new Error(datos.message || 'No se pudo generar el borrador.');
            }
            if (!datos.borrador || !campos.every(function (campo) {
                return typeof datos.borrador[campo] === 'string' && datos.borrador[campo].trim() !== '';
            })) {
                throw new Error('La propuesta recibida no tiene el formato esperado.');
            }
            if (!coinciden(valoresActuales(), textosAntesDeEnviar)) {
                avisar('El borrador llegó, pero editaste la carta durante la generación. Para proteger tus cambios, no se reemplazó ningún campo. Puedes generar otra vez.', true);
                return;
            }
            // El administrador solicitó escribir directamente en los campos, con opción de deshacer.
            anterior = textosAntesDeEnviar;
            aplicado = {
                asunto: datos.borrador.asunto,
                antecedentes: datos.borrador.antecedentes,
                solicitud: datos.borrador.solicitud
            };
            escribir(aplicado);
            deshacer.classList.remove('d-none');
            avisar('Gemini completó los tres campos. Revisa los hechos y la redacción antes de generar el PDF.', false);
        } catch (error) {
            avisar(error.message || 'No se pudo conectar con Gemini. Tu carta permanece sin cambios.', true);
        } finally {
            generando = false;
            generar.disabled = false;
        }
    });
});
</script>
</body>
</html>

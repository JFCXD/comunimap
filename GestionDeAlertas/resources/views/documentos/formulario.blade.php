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
                    <div class="col-md-4">
                        <label class="form-label" for="lugar_emision">Lugar de emisión</label>
                        <input type="text" class="form-control" id="lugar_emision" name="lugar_emision"
                               maxlength="120" value="{{ old('lugar_emision', 'Cochabamba') }}"
                               placeholder="Ej.: Cochabamba" aria-describedby="cm-lugar-ayuda">
                        <small id="cm-lugar-ayuda" class="text-muted">Si queda vacío, se utilizará Cochabamba.</small>
                    </div>
                    <div class="col-md-4">
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
                    {{-- La IA redacta Asunto, Antecedentes y Solicitud: colocarla antes de esos campos. --}}
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
                        <button type="button" id="cmDeshacerIA" class="btn btn-link btn-sm px-0 mt-1 d-none">
                            <i class="fa-solid fa-rotate-left me-1" aria-hidden="true"></i>
                            Deshacer borrador de IA
                        </button>
                        <div id="cmEstadoIA" class="small mt-1" role="status" aria-live="polite"></div>
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
            <button type="button" id="cmAbrirEditorCarta" class="btn btn-outline-primary">
                <i class="fa fa-file-word-o me-1" aria-hidden="true"></i> Vista previa editable (estilo Word)
            </button>
            <button type="submit" name="modo" value="vista_previa" formtarget="_blank" class="btn btn-outline-secondary"><i class="fa fa-eye me-1"></i> Vista previa PDF</button>
            <button type="submit" name="modo" value="descargar" class="btn btn-primary"><i class="fa fa-file-pdf-o me-1"></i> Descargar PDF formal</button>
            <a href="{{ url('/reportes') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>

        {{-- El PDF recibe el formato de los párrafos por separado; el controlador sanea el HTML. --}}
        <input type="hidden" id="cmFormatoAntecedentes" name="antecedentes_html" value="">
        <input type="hidden" id="cmFormatoSolicitud" name="solicitud_html" value="">
        <dialog id="cmEditorCarta" class="cm-editor-dialog" aria-labelledby="cmEditorTitulo">
            <div class="cm-editor-topbar">
                <div>
                    <strong id="cmEditorTitulo">Editor visual de carta</strong>
                    <div class="small">Haz clic en el texto para corregirlo. Los cambios se usarán en el PDF.</div>
                </div>
                <button type="button" id="cmCerrarEditorCarta" class="btn btn-sm btn-outline-secondary">Cerrar editor</button>
            </div>
            <div class="cm-editor-herramientas" role="toolbar" aria-label="Herramientas de formato de Word">
                <select id="cmEditorTamano" class="form-select form-select-sm cm-editor-select" aria-label="Tamaño de letra">
                    <option value="3">Normal</option>
                    <option value="2">Pequeño</option>
                    <option value="4">Mediano</option>
                    <option value="5">Grande</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="bold" title="Negrita (Ctrl+B)"><b>N</b></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="italic" title="Cursiva (Ctrl+I)"><i>C</i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="underline" title="Subrayado (Ctrl+U)"><u>S</u></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="justifyLeft" title="Alinear a la izquierda">Izquierda</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="justifyCenter" title="Centrar">Centro</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="justifyRight" title="Alinear a la derecha">Derecha</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="justifyFull" title="Justificar">Justificar</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="insertUnorderedList" title="Lista con viñetas">• Lista</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-cm-formato="insertOrderedList" title="Lista numerada">1. Lista</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="cmDeshacerEditor">Deshacer</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="cmRehacerEditor">Rehacer</button>
            </div>
            <div class="cm-editor-desplazamiento">
                <article class="cm-editor-hoja" aria-label="Carta editable tamaño A4">
                    <header class="cm-editor-membrete">
                        <strong>O.T.B. UNIVERSITARIO ALTO</strong>
                        <span>RESOLUCIÓN PREFECTURAL N.º 201/96 · RESOLUCIÓN MUNICIPAL N.º 196/96</span>
                        <span>DISTRITO N.º 7 · ZONA UNIVERSITARIO ALTO · COCHABAMBA — BOLIVIA</span>
                    </header>
                    <div class="cm-editor-fecha">
                        <span class="cm-editor-editable" contenteditable="true" data-cm-campo="lugar_emision" role="textbox" aria-label="Lugar de emisión"></span>,
                        <input class="cm-editor-input-fecha" type="date" data-cm-campo="fecha" aria-label="Fecha de emisión">
                    </div>
                    <div class="cm-editor-destinatario">
                        Señor(a):<div class="cm-editor-editable" contenteditable="true" data-cm-campo="destinatario" role="textbox" aria-label="Destinatario"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="cargo_destinatario" role="textbox" aria-label="Cargo del destinatario"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="institucion" role="textbox" aria-label="Institución"></div>
                        Presente.-
                    </div>
                    <div class="cm-editor-ref"><strong>REF.:</strong> <span class="cm-editor-editable" contenteditable="true" data-cm-campo="asunto" role="textbox" aria-label="Asunto"></span></div>
                    <p>De nuestra consideración:</p>
                    <div class="cm-editor-parrafo cm-editor-editable" contenteditable="true" data-cm-campo="antecedentes" role="textbox" aria-label="Antecedentes" aria-multiline="true"></div>
                    <div class="cm-editor-parrafo cm-editor-editable" contenteditable="true" data-cm-campo="solicitud" role="textbox" aria-label="Solicitud" aria-multiline="true"></div>
                    <p>Agradeciendo su atención, nos despedimos con las consideraciones más distinguidas.</p>
                    <p>Atentamente,</p>
                    <div class="cm-editor-firma">
                        <div class="cm-editor-firma-linea"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="remitente" role="textbox" aria-label="Nombre de quien firma"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="cargo_remitente" role="textbox" aria-label="Cargo de quien firma"></div>
                        <small>OTB Barrio Universitario Alto</small>
                        <small>Sello de la OTB</small>
                    </div>
                    <footer>OTB BARRIO UNIVERSITARIO ALTO — COCHABAMBA, BOLIVIA</footer>
                </article>
            </div>
            <div class="cm-editor-acciones">
                <span class="small text-muted">La vista previa editable aproxima el formato. El PDF conserva los logos oficiales y los anexos seleccionados.</span>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" name="modo" value="vista_previa" formtarget="_blank" class="btn btn-outline-primary">Ver PDF final</button>
                    <button type="submit" name="modo" value="descargar" class="btn btn-primary">Descargar PDF</button>
                </div>
            </div>
        </dialog>
    </form>
</div>


@include('plantillas.pie')
</div>
</div>
@include('plantillas.scripts')
<style>
/* Editor de carta aislado de los estilos generales de ComuniMap. */
.cm-editor-dialog { width:min(1150px, 96vw); max-width:none; max-height:96vh; padding:0; border:1px solid #cbd5e1; border-radius:12px; background:#f1f5f9; color:#1f2937; box-shadow:0 20px 70px rgba(0,0,0,.3); }
.cm-editor-dialog::backdrop { background:rgba(15,23,42,.72); }
.cm-editor-topbar,.cm-editor-herramientas,.cm-editor-acciones { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px 18px; background:white; }
.cm-editor-topbar { border-bottom:1px solid #e2e8f0; }
.cm-editor-herramientas { justify-content:flex-start; border-bottom:1px solid #e2e8f0; flex-wrap:wrap; }
.cm-editor-select { width:auto; max-width:125px; }
.cm-editor-parrafo ul,.cm-editor-parrafo ol { padding-left:24px; }
.cm-editor-parrafo p,.cm-editor-parrafo div { margin:0 0 8px; }
.cm-editor-acciones { border-top:1px solid #e2e8f0; flex-wrap:wrap; }
.cm-editor-desplazamiento { padding:24px 12px; overflow:auto; max-height:calc(96vh - 190px); }
.cm-editor-hoja { background:#fff; width:210mm; min-height:297mm; padding:20mm 21mm; margin:auto; box-shadow:0 4px 17px #0002; font:14px/1.65 Georgia,"Times New Roman",serif; color:#222; box-sizing:border-box; }
.cm-editor-membrete { text-align:center; border-bottom:1px solid #334155; margin-bottom:22px; padding-bottom:13px; color:#163b77; display:flex; flex-direction:column; font:10px/1.4 Arial,sans-serif; }
.cm-editor-membrete strong { font-size:17px; font-family:Georgia,serif; }
.cm-editor-fecha { display:flex; justify-content:flex-end; align-items:center; gap:5px; margin-bottom:26px; }
.cm-editor-input-fecha { width:150px; border:1px dashed #cbd5e1; background:transparent; color:inherit; font:inherit; }
.cm-editor-destinatario { margin-bottom:16px; }
.cm-editor-ref { margin:20px 0; }
.cm-editor-parrafo { text-align:justify; margin:0 0 18px; min-height:32px; }
.cm-editor-editable { white-space:pre-wrap; overflow-wrap:anywhere; min-width:65px; min-height:1.5em; border-radius:3px; outline:1px dashed #bfdbfe; padding:1px 3px; }
.cm-editor-editable:focus { outline:2px solid #2563eb; background:#eff6ff; }
.cm-editor-destinatario .cm-editor-editable, .cm-editor-firma .cm-editor-editable { display:block; }
.cm-editor-firma { margin:62px auto 0; width:75%; text-align:center; }
.cm-editor-firma-linea { width:70%; margin:auto auto 8px; border-top:1px dotted #333; }
.cm-editor-firma small { display:block; }
.cm-editor-hoja footer { border-top:1px solid #999; margin-top:90px; padding-top:8px; text-align:center; font:10px Arial,sans-serif; }
@media(max-width:850px){ .cm-editor-hoja { width:100%; min-height:680px; padding:25px 22px; } .cm-editor-desplazamiento { padding:10px; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Vista previa editable: mantiene los campos originales como única fuente
    // para PDF. No se envía HTML arbitrario al servidor.
    const editor = document.getElementById('cmEditorCarta');
    const abrirEditor = document.getElementById('cmAbrirEditorCarta');
    const cerrarEditor = document.getElementById('cmCerrarEditorCarta');
    const ediciones = Array.from(editor.querySelectorAll('[data-cm-campo]'));
    const formatoOculto = { antecedentes: document.getElementById('cmFormatoAntecedentes'), solicitud: document.getElementById('cmFormatoSolicitud') };
    const editableTexto = function (nodo) { return nodo && nodo.isContentEditable && ['antecedentes','solicitud'].includes(nodo.dataset.cmCampo); };
    let ultimoTextoActivo = null;
    let restaurandoFormato = false;
    const historialEditor = [];
    let indiceHistorial = -1;
    let cargandoEditor = false;

    function valoresCarta() {
        const resultado = {};
        ediciones.forEach(function (elemento) {
            const campo = elemento.dataset.cmCampo;
            const origen = document.getElementById(campo);
            resultado[campo] = origen ? origen.value : '';
        });
        return resultado;
    }
    function cargarHoja(valores) {
        cargandoEditor = true;
        ediciones.forEach(function (elemento) {
            const valor = valores[elemento.dataset.cmCampo] || '';
            if (elemento.isContentEditable) {
                const oculto = formatoOculto[elemento.dataset.cmCampo];
                const textoOriginal = document.getElementById(elemento.dataset.cmCampo);
                if (oculto && oculto.value && oculto.dataset.texto === textoOriginal.value) {
                    elemento.innerHTML = oculto.value;
                } else {
                    elemento.innerText = valor;
                    if (oculto) oculto.value = '';
                }
            } else elemento.value = valor;
        });
        cargandoEditor = false;
    }
    function guardarVersion() {
        if (cargandoEditor) return;
        const valor = JSON.stringify(valoresCarta());
        if (historialEditor[indiceHistorial] === valor) return;
        historialEditor.splice(indiceHistorial + 1);
        historialEditor.push(valor);
        if (historialEditor.length > 40) historialEditor.shift();
        indiceHistorial = historialEditor.length - 1;
    }
    function restaurarVersion(indice) {
        if (indice < 0 || indice >= historialEditor.length) return;
        indiceHistorial = indice;
        const valores = JSON.parse(historialEditor[indiceHistorial]);
        Object.keys(valores).forEach(function (campo) {
            const origen = document.getElementById(campo);
            if (origen) origen.value = valores[campo];
        });
        cargarHoja(valores);
    }
    abrirEditor.addEventListener('click', function () {
        historialEditor.length = 0;
        indiceHistorial = -1;
        cargarHoja(valoresCarta());
        guardarVersion();
        editor.showModal();
    });
    cerrarEditor.addEventListener('click', function () { editor.close(); });
    ediciones.forEach(function (elemento) {
        elemento.addEventListener('focus', function () {
            if (editableTexto(elemento)) ultimoTextoActivo = elemento;
        });
        elemento.addEventListener('input', function () {
            if (cargandoEditor) return;
            const origen = document.getElementById(elemento.dataset.cmCampo);
            if (!origen) return;
            origen.value = elemento.isContentEditable ? elemento.innerText : elemento.value;
            if (editableTexto(elemento)) {
                formatoOculto[elemento.dataset.cmCampo].value = elemento.innerHTML;
                formatoOculto[elemento.dataset.cmCampo].dataset.texto = origen.value;
            }
            guardarVersion();
        });
        // Los campos de una línea no deben insertar saltos.
        if (elemento.isContentEditable && !['antecedentes', 'solicitud'].includes(elemento.dataset.cmCampo)) {
            elemento.addEventListener('keydown', function (evento) {
                if (evento.key === 'Enter') evento.preventDefault();
            });
        }
    });
    // Controles de formato nativos del navegador. Solo actúan sobre los dos
    // párrafos editables, nunca sobre el membrete ni sobre otros formularios.
    editor.querySelectorAll('[data-cm-formato]').forEach(function (boton) {
        boton.addEventListener('mousedown', function (evento) { evento.preventDefault(); });
        boton.addEventListener('click', function () {
            if (!editableTexto(ultimoTextoActivo)) return;
            ultimoTextoActivo.focus();
            document.execCommand(boton.dataset.cmFormato, false, null);
            ultimoTextoActivo.dispatchEvent(new Event('input', { bubbles:true }));
        });
    });
    const tamanoEditor = document.getElementById('cmEditorTamano');
    tamanoEditor.addEventListener('mousedown', function () {
        // El navegador mantiene la selección mientras se elige un tamaño.
    });
    tamanoEditor.addEventListener('change', function () {
        if (!editableTexto(ultimoTextoActivo)) return;
        ultimoTextoActivo.focus();
        document.execCommand('fontSize', false, tamanoEditor.value);
        ultimoTextoActivo.dispatchEvent(new Event('input', { bubbles:true }));
    });
    ['antecedentes', 'solicitud'].forEach(function (campo) {
        document.getElementById(campo).addEventListener('input', function () {
            if (editor.open) return;
            formatoOculto[campo].value = '';
            formatoOculto[campo].dataset.texto = '';
        });
    });
    document.getElementById('cmDeshacerEditor').addEventListener('click', function () { restaurarVersion(indiceHistorial - 1); });
    document.getElementById('cmRehacerEditor').addEventListener('click', function () { restaurarVersion(indiceHistorial + 1); });
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

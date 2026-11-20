@include('plantillas.encabezado')
<body>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
@include('plantillas.encabezado2')
@include('plantillas.menu')
<div class="page-wrapper">
<main class="container-fluid py-3 cm-editor-pagina">
    @if($errors->any())
    <div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    @endif
    <form id="cmFormularioCarta" action="{{ route('documentos.descargar') }}" method="POST">
        @csrf
        @foreach($datos['reportes'] as $id)<input type="hidden" name="reportes[]" value="{{ $id }}">@endforeach
        @php($camposInstitucionales = ['membrete_titulo','membrete_prefectural','membrete_municipal','membrete_registro','membrete_zona','tratamiento','presente','referencia_etiqueta','saludo','despedida','atentamente','firma_otb','sello_etiqueta','pie_carta'])
        @foreach($datos as $campo => $valor)
            @if($campo !== 'reportes' && !in_array($campo, $camposInstitucionales, true) && !str_ends_with($campo, '_html') && is_scalar($valor))
                <input type="hidden" id="{{ $campo }}" name="{{ $campo }}" value="{{ $valor }}">
            @endif
        @endforeach
        <input type="hidden" id="membrete_titulo" name="membrete_titulo" value="{{ ($datos['membrete_titulo'] ?? 'O.T.B. UNIVERSITARIO ALTO') }}">
        <input type="hidden" id="membrete_prefectural" name="membrete_prefectural" value="{{ ($datos['membrete_prefectural'] ?? 'RESOLUCIÓN PREFECTURAL N.º 201/96 DE FECHA 15/11/1996') }}">
        <input type="hidden" id="membrete_municipal" name="membrete_municipal" value="{{ ($datos['membrete_municipal'] ?? 'RESOLUCIÓN MUNICIPAL N.º 196/96 DE FECHA 01/11/1996') }}">
        <input type="hidden" id="membrete_registro" name="membrete_registro" value="{{ ($datos['membrete_registro'] ?? 'REGISTRO D.S. 21060 — MUNICIPIO DE COCHABAMBA') }}">
        <input type="hidden" id="membrete_zona" name="membrete_zona" value="{{ ($datos['membrete_zona'] ?? 'DISTRITO N.º 7 · ZONA UNIVERSITARIO ALTO · COCHABAMBA — BOLIVIA') }}">
        <input type="hidden" id="tratamiento" name="tratamiento" value="{{ ($datos['tratamiento'] ?? 'Señor(a):') }}">
        <input type="hidden" id="presente" name="presente" value="{{ ($datos['presente'] ?? 'Presente.-') }}">
        <input type="hidden" id="referencia_etiqueta" name="referencia_etiqueta" value="{{ ($datos['referencia_etiqueta'] ?? 'REF.:') }}">
        <input type="hidden" id="saludo" name="saludo" value="{{ ($datos['saludo'] ?? 'De nuestra consideración:') }}">
        <input type="hidden" id="despedida" name="despedida" value="{{ ($datos['despedida'] ?? 'Agradeciendo su atención, nos despedimos con las consideraciones más distinguidas.') }}">
        <input type="hidden" id="atentamente" name="atentamente" value="{{ ($datos['atentamente'] ?? 'Atentamente,') }}">
        <input type="hidden" id="firma_otb" name="firma_otb" value="{{ ($datos['firma_otb'] ?? 'OTB Barrio Universitario Alto') }}">
        <input type="hidden" id="sello_etiqueta" name="sello_etiqueta" value="{{ ($datos['sello_etiqueta'] ?? 'Sello de la OTB') }}">
        <input type="hidden" id="pie_carta" name="pie_carta" value="{{ ($datos['pie_carta'] ?? 'OTB BARRIO UNIVERSITARIO ALTO — COCHABAMBA, BOLIVIA') }}">
        {{-- El PDF recibe el formato de los párrafos por separado; el controlador sanea el HTML. --}}
        <input type="hidden" id="cmFormatoAntecedentes" name="antecedentes_html" value="">
        <input type="hidden" id="cmFormatoSolicitud" name="solicitud_html" value="">
        <input type="hidden" id="membrete_titulo_html" name="membrete_titulo_html" value="">
        <input type="hidden" id="membrete_prefectural_html" name="membrete_prefectural_html" value="">
        <input type="hidden" id="membrete_municipal_html" name="membrete_municipal_html" value="">
        <input type="hidden" id="membrete_registro_html" name="membrete_registro_html" value="">
        <input type="hidden" id="membrete_zona_html" name="membrete_zona_html" value="">
        <input type="hidden" id="tratamiento_html" name="tratamiento_html" value="">
        <input type="hidden" id="presente_html" name="presente_html" value="">
        <input type="hidden" id="referencia_etiqueta_html" name="referencia_etiqueta_html" value="">
        <input type="hidden" id="saludo_html" name="saludo_html" value="">
        <input type="hidden" id="despedida_html" name="despedida_html" value="">
        <input type="hidden" id="atentamente_html" name="atentamente_html" value="">
        <input type="hidden" id="firma_otb_html" name="firma_otb_html" value="">
        <input type="hidden" id="sello_etiqueta_html" name="sello_etiqueta_html" value="">
        <input type="hidden" id="pie_carta_html" name="pie_carta_html" value="">
        
<section id="cmEditorCarta" class="cm-editor-dialog" aria-labelledby="cmEditorTitulo">
            <div class="cm-editor-topbar">
                <div>
                    <strong id="cmEditorTitulo">Editor visual de carta</strong>
                    <div class="small">Edita directamente la carta. Selecciona texto para cambiar su formato y descarga el PDF al terminar.</div>
                </div>
                <button type="button" id="cmCerrarEditorCarta" class="btn btn-sm btn-outline-secondary">Volver al formulario</button>
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
                        <strong class="cm-editor-editable" contenteditable="true" data-cm-campo="membrete_titulo" role="textbox" aria-label="membrete titulo"></strong>
                        <span class="cm-editor-editable" contenteditable="true" data-cm-campo="membrete_prefectural" role="textbox" aria-label="membrete prefectural"></span>
                        <span class="cm-editor-editable" contenteditable="true" data-cm-campo="membrete_municipal" role="textbox" aria-label="membrete municipal"></span>
                        <span class="cm-editor-editable" contenteditable="true" data-cm-campo="membrete_registro" role="textbox" aria-label="membrete registro"></span>
                        <span class="cm-editor-editable" contenteditable="true" data-cm-campo="membrete_zona" role="textbox" aria-label="membrete zona"></span>
                    </header>
                    <div class="cm-editor-fecha">
                        <span class="cm-editor-editable" contenteditable="true" data-cm-campo="lugar_emision" role="textbox" aria-label="Lugar de emisión"></span>,
                        <input class="cm-editor-input-fecha" type="date" data-cm-campo="fecha" aria-label="Fecha de emisión">
                    </div>
                    <div class="cm-editor-destinatario">
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="tratamiento" role="textbox" aria-label="tratamiento"></div><div class="cm-editor-editable" contenteditable="true" data-cm-campo="destinatario" role="textbox" aria-label="Destinatario"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="cargo_destinatario" role="textbox" aria-label="Cargo del destinatario"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="institucion" role="textbox" aria-label="Institución"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="presente" role="textbox" aria-label="presente"></div>
                    </div>
                    <div class="cm-editor-ref"><strong class="cm-editor-editable" contenteditable="true" data-cm-campo="referencia_etiqueta" role="textbox" aria-label="referencia etiqueta"></strong> <span class="cm-editor-editable" contenteditable="true" data-cm-campo="asunto" role="textbox" aria-label="Asunto"></span></div>
                    <p class="cm-editor-editable" contenteditable="true" data-cm-campo="saludo" role="textbox" aria-label="saludo"></p>
                    <div class="cm-editor-parrafo cm-editor-editable" contenteditable="true" data-cm-campo="antecedentes" role="textbox" aria-label="Antecedentes" aria-multiline="true"></div>
                    <div class="cm-editor-parrafo cm-editor-editable" contenteditable="true" data-cm-campo="solicitud" role="textbox" aria-label="Solicitud" aria-multiline="true"></div>
                    <p class="cm-editor-editable" contenteditable="true" data-cm-campo="despedida" role="textbox" aria-label="despedida"></p>
                    <p class="cm-editor-editable" contenteditable="true" data-cm-campo="atentamente" role="textbox" aria-label="atentamente"></p>
                    <div class="cm-editor-firma">
                        <div class="cm-editor-firma-linea"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="remitente" role="textbox" aria-label="Nombre de quien firma"></div>
                        <div class="cm-editor-editable" contenteditable="true" data-cm-campo="cargo_remitente" role="textbox" aria-label="Cargo de quien firma"></div>
                        <small class="cm-editor-editable" contenteditable="true" data-cm-campo="firma_otb" role="textbox" aria-label="firma otb"></small>
                        <small class="cm-editor-editable" contenteditable="true" data-cm-campo="sello_etiqueta" role="textbox" aria-label="sello etiqueta"></small>
                    </div>
                    <footer><span class="cm-editor-editable" contenteditable="true" data-cm-campo="pie_carta" role="textbox" aria-label="pie carta"></span></footer>
                </article>
            </div>
            <div class="cm-editor-acciones">
                <span class="small text-muted">Todos los textos son editables. Los logos y las fotos permanecen protegidos; el PDF conserva los elementos institucionales.</span>
                <div class="d-flex gap-2 flex-wrap">
                    
                    <button type="submit" name="modo" value="vista_previa" formtarget="_blank" class="btn btn-primary"><i class="fa fa-file-pdf-o me-1"></i> Descargar PDF</button>
                </div>
            </div>
        </section>
    </form>
</main>
@include('plantillas.pie')
</div></div>
@include('plantillas.scripts')
<style>
/* Editor de carta aislado de los estilos generales de ComuniMap. */
.cm-editor-dialog { width:100%; max-width:none; padding:0; border:1px solid #cbd5e1; border-radius:12px; background:#f1f5f9; color:#1f2937; box-shadow:none; }
.cm-editor-dialog::backdrop { background:rgba(15,23,42,.72); }
.cm-editor-topbar,.cm-editor-herramientas,.cm-editor-acciones { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px 18px; background:white; }
.cm-editor-topbar { border-bottom:1px solid #e2e8f0; }
.cm-editor-herramientas { justify-content:flex-start; border-bottom:1px solid #e2e8f0; flex-wrap:wrap; }
.cm-editor-select { width:auto; max-width:125px; }
.cm-editor-parrafo ul,.cm-editor-parrafo ol { padding-left:24px; }
.cm-editor-parrafo p,.cm-editor-parrafo div { margin:0 0 8px; }
.cm-editor-acciones { border-top:1px solid #e2e8f0; flex-wrap:wrap; }
.cm-editor-desplazamiento { padding:24px 12px; overflow:auto; flex:1 1 auto; min-height:0; max-height:none; }
.cm-editor-hoja { background:#fff; width:210mm; min-height:297mm; padding:20mm 21mm; margin:auto; box-shadow:0 4px 17px #0002; font:14px/1.65 Georgia,"Times New Roman",serif; color:#222; box-sizing:border-box; }
.cm-editor-membrete { text-align:center; border-bottom:1px solid #334155; margin-bottom:22px; padding-bottom:13px; color:#163b77; display:flex; flex-direction:column; font:10px/1.4 Arial,sans-serif; }
.cm-editor-membrete strong { font-size:17px; font-family:Georgia,serif; }
.cm-editor-membrete .cm-editor-editable { display:block; }
.cm-editor-firma small.cm-editor-editable { display:block; }
.cm-editor-hoja footer .cm-editor-editable { display:block; }
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
<style>
.cm-editor-pagina { max-width:none; padding-left:12px; padding-right:12px; }
.cm-editor-pagina .cm-editor-hoja { width:210mm; max-width:none; }
.cm-editor-pagina .cm-editor-dialog { border-radius:6px; display:flex; flex-direction:column; height:calc(100vh - 115px); height:calc(100dvh - 115px); overflow:hidden; }
.cm-editor-pagina .cm-editor-topbar,.cm-editor-pagina .cm-editor-herramientas,.cm-editor-pagina .cm-editor-acciones { flex-shrink:0; }
.cm-editor-pagina .cm-editor-acciones { position:relative; z-index:2; }
@media (max-width:940px) { .cm-editor-pagina .cm-editor-desplazamiento { overflow-x:auto; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Vista previa editable: mantiene los campos originales como única fuente
    // para PDF. No se envía HTML arbitrario al servidor.
    const editor = document.getElementById('cmEditorCarta');
    const cerrarEditor = document.getElementById('cmCerrarEditorCarta');
    const ediciones = Array.from(editor.querySelectorAll('[data-cm-campo]'));
    const formatoOculto = { antecedentes: document.getElementById('cmFormatoAntecedentes'), solicitud: document.getElementById('cmFormatoSolicitud') };
    ["membrete_titulo","membrete_prefectural","membrete_municipal","membrete_registro","membrete_zona","tratamiento","presente","referencia_etiqueta","saludo","despedida","atentamente","firma_otb","sello_etiqueta","pie_carta"].forEach(function (campo) {
        formatoOculto[campo] = document.getElementById(campo + '_html');
    });
    const editableTexto = function (nodo) { return nodo && nodo.isContentEditable; };
    let ultimoTextoActivo = null;
    let seleccionGuardada = null;
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
        const valor = JSON.stringify({
            campos: valoresCarta(),
            html: Object.fromEntries(Object.keys(formatoOculto).map(function (campo) {
                return [campo, formatoOculto[campo].value];
            }))
        });
        if (historialEditor[indiceHistorial] === valor) return;
        historialEditor.splice(indiceHistorial + 1);
        historialEditor.push(valor);
        if (historialEditor.length > 40) historialEditor.shift();
        indiceHistorial = historialEditor.length - 1;
    }
    function restaurarVersion(indice) {
        if (indice < 0 || indice >= historialEditor.length) return;
        indiceHistorial = indice;
        const version = JSON.parse(historialEditor[indiceHistorial]);
        const valores = version.campos;
        Object.keys(valores).forEach(function (campo) {
            const origen = document.getElementById(campo);
            if (origen) origen.value = valores[campo];
        });
        Object.keys(formatoOculto).forEach(function (campo) {
            formatoOculto[campo].value = version.html[campo] || '';
            formatoOculto[campo].dataset.texto = valores[campo] || '';
        });
        cargarHoja(valores);
    }
    cargarHoja(valoresCarta());
    guardarVersion();
    ultimoTextoActivo = null;
    cerrarEditor.addEventListener('click', function () { window.history.back(); });
    ediciones.forEach(function (elemento) {
        elemento.addEventListener('focus', function () {
            if (elemento.isContentEditable) ultimoTextoActivo = elemento;
        });
        elemento.addEventListener('input', function () {
            if (cargandoEditor) return;
            const origen = document.getElementById(elemento.dataset.cmCampo);
            if (!origen) return;
            origen.value = elemento.isContentEditable ? elemento.innerText : elemento.value;
            if (editableTexto(elemento) && formatoOculto[elemento.dataset.cmCampo]) {
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
    // Recordar la selección al usar la barra; no afecta los otros campos
    // ni permite editar gráficos, membretes de imagen o archivos anexos.
    document.addEventListener('selectionchange', function () {
        if (!document.body.contains(editor)) return;
        const seleccion = window.getSelection();
        if (!seleccion || seleccion.rangeCount === 0) return;
        const nodo = seleccion.anchorNode;
        const elemento = nodo && (nodo.nodeType === 1 ? nodo : nodo.parentElement);
        const editable = elemento && elemento.closest ? elemento.closest('.cm-editor-editable') : null;
        if (editable && editor.contains(editable)) {
            ultimoTextoActivo = editable;
            seleccionGuardada = seleccion.getRangeAt(0).cloneRange();
        }
    });
    function activarSeleccion() {
        if (!editableTexto(ultimoTextoActivo)) return false;
        ultimoTextoActivo.focus();
        if (seleccionGuardada) {
            const seleccion = window.getSelection();
            seleccion.removeAllRanges();
            seleccion.addRange(seleccionGuardada);
        }
        return true;
    }
    // Formato permitido en todos los textos editables de la hoja.
    editor.querySelectorAll('[data-cm-formato]').forEach(function (boton) {
        boton.addEventListener('mousedown', function (evento) { evento.preventDefault(); });
        boton.addEventListener('click', function () {
            if (!activarSeleccion()) return;
            document.execCommand(boton.dataset.cmFormato, false, null);
            ultimoTextoActivo.dispatchEvent(new Event('input', { bubbles:true }));
        });
    });
    const tamanoEditor = document.getElementById('cmEditorTamano');
    tamanoEditor.addEventListener('change', function () {
        if (!activarSeleccion()) return;
        document.execCommand('fontSize', false, tamanoEditor.value);
        ultimoTextoActivo.dispatchEvent(new Event('input', { bubbles:true }));
    });
    Object.keys(formatoOculto).forEach(function (campo) {
        document.getElementById(campo).addEventListener('input', function () {
            if (document.activeElement && editor.contains(document.activeElement)) return;
            formatoOculto[campo].value = '';
            formatoOculto[campo].dataset.texto = '';
        });
    });
    document.getElementById('cmDeshacerEditor').addEventListener('click', function () { restaurarVersion(indiceHistorial - 1); });
    document.getElementById('cmRehacerEditor').addEventListener('click', function () { restaurarVersion(indiceHistorial + 1); });

});
</script>
</body></html>
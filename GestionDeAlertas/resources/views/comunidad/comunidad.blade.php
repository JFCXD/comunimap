@include('plantillas.encabezado')
<body>
<div class="preloader"><div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div></div>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
@include('plantillas.encabezado2')
@include('plantillas.menu')
<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="cm-page-heading">
                    <h4 class="page-title">Comunidad</h4>
                    <p class="cm-subtitle mb-0">Comunicados, actividades y encuestas de la OTB Universidad Alto.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <style>
        .cm-muro{max-width:790px;margin:0 auto}.cm-post{border:1px solid #e2e8f0;border-radius:17px;overflow:hidden;box-shadow:0 4px 18px rgba(16,34,65,.045)}
        .cm-post-head{display:flex;align-items:center;gap:12px}.cm-avatar{display:grid;place-items:center;width:44px;height:44px;border-radius:50%;background:#edf3ff;color:#2563eb}
        .cm-post-image{width:100%;max-height:390px;object-fit:cover}.cm-option{display:block;border:1px solid #e0e8f4;padding:10px 13px;border-radius:10px;margin-bottom:9px;cursor:pointer}
        .cm-option input{margin-right:9px}.cm-result{height:8px;background:#edf2f9;border-radius:30px;overflow:hidden}.cm-result>div{height:100%;background:#2563eb}
        .cm-post-text{white-space:pre-wrap;overflow-wrap:anywhere}.cm-crear label{font-weight:600;font-size:14px;margin-bottom:5px}
        .cm-crear .form-control,.cm-crear .form-select{border-radius:9px}.cm-card-pad{padding:22px}
        @media(max-width:576px){.cm-card-pad{padding:16px}.cm-post-image{max-height:280px}}
        </style>
        <div class="cm-muro">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if($errors->any())
          <div class="alert alert-danger"><strong>Revisa los datos:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        @if($admin)
        <div class="card cm-post mb-4">
          <div class="card-body cm-card-pad">
            <h5 class="mb-1"><i class="fas fa-pen-to-square text-primary me-2"></i>Crear publicación</h5>
            <p class="text-muted small mb-3">Solo los administradores pueden publicar. Los vecinos no pueden comentar.</p>
            <form method="POST" action="{{ route('comunidad.guardar') }}" enctype="multipart/form-data" class="cm-crear" id="formComunidad">
              @csrf
              <div class="row g-3">
                <div class="col-sm-6"><label for="cmTipo">Tipo de publicación</label>
                  <select id="cmTipo" name="tipo" class="form-select form-control" required>
                    @foreach(['Comunicado','Actividad','Encuesta'] as $tipo)
                    <option value="{{ $tipo }}" {{ old('tipo','Comunicado')===$tipo?'selected':'' }}>{{ $tipo }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-sm-6"><label for="cmEstado">Visibilidad</label>
                  <select id="cmEstado" name="estado" class="form-select form-control">
                    <option value="Borrador" {{ old('estado')==='Borrador'?'selected':'' }}>Guardar borrador</option>
                    <option value="Publicado" {{ old('estado')==='Publicado'?'selected':'' }}>Publicar ahora</option>
                  </select>
                </div>
                <div class="col-12"><label for="cmTitulo">Título</label><input id="cmTitulo" name="titulo" value="{{ old('titulo') }}" class="form-control" maxlength="180" required></div>
                <div class="col-12"><label for="cmDescripcion">Descripción</label><textarea id="cmDescripcion" name="descripcion" class="form-control" rows="4" required maxlength="10000">{{ old('descripcion') }}</textarea></div>
                <div class="col-12" id="cmImagen"><label for="cmFoto">Fotografía (opcional, hasta 4 MB)</label><input id="cmFoto" type="file" name="imagen" accept="image/jpeg,image/png,image/webp" class="form-control"></div>
                <div class="col-12" id="cmCamposActividad" style="display:none">
                  <div class="row g-3">
                    <div class="col-md-4"><label for="cmFechaActividad">Fecha de actividad</label><input id="cmFechaActividad" name="fechaActividad" value="{{ old('fechaActividad') }}" type="date" class="form-control"></div>
                    <div class="col-md-4"><label for="cmHoraActividad">Hora</label><input id="cmHoraActividad" name="horaActividad" value="{{ old('horaActividad') }}" type="time" class="form-control"></div>
                    <div class="col-md-4"><label for="cmLugarActividad">Lugar</label><input id="cmLugarActividad" name="lugarActividad" value="{{ old('lugarActividad') }}" maxlength="255" class="form-control"></div>
                  </div>
                </div>
                <div class="col-12" id="cmCamposEncuesta" style="display:none">
                  <div class="mb-3"><label for="cmFechaCierre">Fecha y hora de cierre</label><input id="cmFechaCierre" name="fechaCierre" type="datetime-local" value="{{ old('fechaCierre') }}" class="form-control"></div>
                  <label>Opciones de respuesta (entre 2 y 10)</label>
                  <div id="cmOpciones">
                    @foreach(old('opciones', ['', '']) as $respuesta)
                    <input name="opciones[]" type="text" maxlength="255" class="form-control mb-2" value="{{ $respuesta }}" placeholder="Escribe una opción">
                    @endforeach
                  </div>
                  <button type="button" id="cmAgregarOpcion" class="btn btn-outline-primary btn-sm">+ Añadir opción</button>
                </div>
              </div>
              <div class="mt-3"><button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane me-1"></i> Guardar publicación</button></div>
            </form>
          </div>
        </div>
        @endif

        @forelse($publicaciones as $pub)
        @php
          $lista = $opciones->get($pub->idPublicacion, collect());
          $totalVotos = $lista->sum('total');
          $miVoto = $misVotos->get($pub->idPublicacion);
          $abierta = $pub->tipo === 'Encuesta' && $pub->estado === 'Publicado' && $pub->fechaCierre && \Carbon\Carbon::parse($pub->fechaCierre)->isFuture();
        @endphp
        <article class="card cm-post mb-4" id="publicacion-{{ $pub->idPublicacion }}">
          <div class="card-body cm-card-pad">
            <div class="cm-post-head mb-3">
              <div class="cm-avatar"><i class="fas fa-building-user"></i></div>
              <div class="flex-grow-1">
                <strong>OTB Universidad Alto</strong>
                <div class="text-muted small">{{ $pub->tipo }} · {{ \Carbon\Carbon::parse($pub->fechaCreacion)->format('d/m/Y H:i') }}</div>
              </div>
              @if($admin)<span class="badge {{ $pub->estado === 'Publicado' ? 'bg-success' : ($pub->estado === 'Borrador' ? 'bg-secondary' : 'bg-warning text-dark') }}">{{ $pub->estado }}</span>@endif
            </div>
            <h5 class="mb-3">{{ $pub->titulo }}</h5>
            <p class="cm-post-text">{{ $pub->descripcion }}</p>
            @if($pub->tipo === 'Actividad')
            <div class="p-3 rounded mb-3" style="background:#f2f6fd">
              <div><i class="far fa-calendar-alt text-primary me-2"></i>Fecha: {{ $pub->fechaActividad ? \Carbon\Carbon::parse($pub->fechaActividad)->format('d/m/Y') : 'Por definir' }}</div>
              <div><i class="far fa-clock text-primary me-2"></i>Hora: {{ $pub->horaActividad ? substr($pub->horaActividad,0,5) : 'Por definir' }}</div>
              <div><i class="fas fa-location-dot text-primary me-2"></i>Lugar: {{ $pub->lugarActividad ?? 'Por definir' }}</div>
            </div>
            @endif
            @if($pub->tipo === 'Encuesta')
            <div class="p-3 border rounded mb-3">
              <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                <strong><i class="fas fa-poll me-1 text-primary"></i> Votación comunitaria</strong>
                <span class="small text-muted">{{ $abierta ? 'Abierta hasta '.\Carbon\Carbon::parse($pub->fechaCierre)->format('d/m/Y H:i') : 'Votación cerrada o no publicada' }}</span>
              </div>
              @if($abierta && !$miVoto && $lista->count() >= 2)
              <form method="POST" action="{{ route('comunidad.votar', $pub->idPublicacion) }}">
                @csrf
                @foreach($lista as $op)
                <label class="cm-option"><input required type="radio" name="idOpcion" value="{{ $op->idOpcion }}">{{ $op->texto }}</label>
                @endforeach
                <button type="submit" class="btn btn-primary btn-sm">Registrar mi voto</button>
              </form>
              @else
              @foreach($lista as $op)
              <div class="mb-3">
                <div class="d-flex justify-content-between small mb-1"><span>{{ $op->texto }} @if($miVoto == $op->idOpcion)<strong>(Tu voto)</strong>@endif</span><strong>{{ $totalVotos ? round($op->total * 100 / $totalVotos) : 0 }}%</strong></div>
                <div class="cm-result"><div style="width:{{ $totalVotos ? round($op->total * 100 / $totalVotos) : 0 }}%"></div></div>
              </div>
              @endforeach
              @if($miVoto)<p class="small text-success mb-0"><i class="fas fa-check-circle me-1"></i>Ya participaste en esta encuesta.</p>@endif
              @endif
              <div class="small text-muted mt-2">{{ $totalVotos }} {{ $totalVotos == 1 ? 'voto registrado' : 'votos registrados' }}</div>
            </div>
            @endif
          </div>
          @if($pub->imagen)
          <img class="cm-post-image" src="{{ asset($pub->imagen) }}" alt="Imagen de {{ $pub->titulo }}" loading="lazy">
          @endif
          @if($admin)
          <div class="card-footer bg-white">
            <details class="mb-3">
              <summary class="text-primary" style="cursor:pointer"><i class="fas fa-edit me-1"></i> Editar publicación</summary>
              <div class="pt-3">
                <form method="POST" action="{{ route('comunidad.actualizar', $pub->idPublicacion) }}">
                  @csrf
                  <div class="mb-2"><label class="form-label fw-semibold" for="editTitulo-{{ $pub->idPublicacion }}">Título</label>
                    <input id="editTitulo-{{ $pub->idPublicacion }}" name="titulo" class="form-control" maxlength="180" required value="{{ $pub->titulo }}"></div>
                  <div class="mb-2"><label class="form-label fw-semibold" for="editDescripcion-{{ $pub->idPublicacion }}">Descripción</label>
                    <textarea id="editDescripcion-{{ $pub->idPublicacion }}" name="descripcion" rows="4" class="form-control" maxlength="10000" required>{{ $pub->descripcion }}</textarea></div>
                  @if($pub->tipo === 'Actividad')
                  <div class="row g-2 mb-2">
                    <div class="col-md-4"><label class="form-label" for="editFecha-{{ $pub->idPublicacion }}">Fecha</label><input id="editFecha-{{ $pub->idPublicacion }}" name="fechaActividad" type="date" class="form-control" value="{{ $pub->fechaActividad }}" required></div>
                    <div class="col-md-4"><label class="form-label" for="editHora-{{ $pub->idPublicacion }}">Hora</label><input id="editHora-{{ $pub->idPublicacion }}" name="horaActividad" type="time" class="form-control" value="{{ $pub->horaActividad ? substr($pub->horaActividad,0,5) : '' }}" required></div>
                    <div class="col-md-4"><label class="form-label" for="editLugar-{{ $pub->idPublicacion }}">Lugar</label><input id="editLugar-{{ $pub->idPublicacion }}" name="lugarActividad" class="form-control" value="{{ $pub->lugarActividad }}" required maxlength="255"></div>
                  </div>
                  @endif
                  @if($pub->tipo === 'Encuesta')
                  <div class="mb-2"><label class="form-label" for="editCierre-{{ $pub->idPublicacion }}">Modificar cierre (solo antes de recibir votos)</label>
                    <input id="editCierre-{{ $pub->idPublicacion }}" name="fechaCierre" type="datetime-local" class="form-control" value="{{ $pub->fechaCierre ? \Carbon\Carbon::parse($pub->fechaCierre)->format('Y-m-d\\TH:i') : '' }}">
                    <div class="small text-muted mt-1">Las opciones de una encuesta no se modifican una vez creada, para conservar la validez de los votos.</div>
                  </div>
                  @endif
                  <button class="btn btn-primary btn-sm" type="submit">Guardar cambios</button>
                </form>
              </div>
            </details>
            <form method="POST" action="{{ route('comunidad.estado', $pub->idPublicacion) }}" class="d-flex align-items-center flex-wrap gap-2">
              @csrf
              <label class="small mb-0" for="estado-{{ $pub->idPublicacion }}">Estado:</label>
              <select id="estado-{{ $pub->idPublicacion }}" name="estado" class="form-select form-control form-control-sm" style="max-width:180px">
                @foreach(['Borrador','Publicado','Archivado'] as $estado)
                <option value="{{ $estado }}" {{ $pub->estado === $estado ? 'selected':'' }}>{{ $estado }}</option>
                @endforeach
              </select>
              <button type="submit" class="btn btn-outline-primary btn-sm">Actualizar estado</button>
            </form>
          </div>
          @endif
        </article>
        @empty
        <div class="card cm-post"><div class="card-body text-center py-5"><i class="fas fa-bullhorn fa-2x text-primary mb-3"></i><h5>Aún no hay publicaciones</h5><p class="text-muted mb-0">Los comunicados y las actividades de la OTB aparecerán aquí.</p></div></div>
        @endforelse
        <div class="mb-4">{{ $publicaciones->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
    @include('plantillas.pie')
</div></div>
@include('plantillas.scripts')
@if($admin)
<script>
(function(){
    var tipo = document.getElementById('cmTipo');
    var actividad = document.getElementById('cmCamposActividad');
    var encuesta = document.getElementById('cmCamposEncuesta');
    function actualizar(){
        var esActividad = tipo.value === 'Actividad', esEncuesta = tipo.value === 'Encuesta';
        actividad.style.display = esActividad ? '' : 'none';
        encuesta.style.display = esEncuesta ? '' : 'none';
        ['cmFechaActividad','cmHoraActividad','cmLugarActividad'].forEach(function(id){
            document.getElementById(id).required = esActividad;
        });
        document.getElementById('cmFechaCierre').required = esEncuesta;
        document.querySelectorAll('#cmOpciones input').forEach(function(input){input.required = esEncuesta;});
    }
    tipo.addEventListener('change', actualizar);
    document.getElementById('cmAgregarOpcion').addEventListener('click',function(){
        var contenedor = document.getElementById('cmOpciones');
        if(contenedor.children.length >= 10)return;
        var input=document.createElement('input');
        input.name='opciones[]';input.type='text';input.maxLength=255;input.required=true;
        input.placeholder='Otra opción';input.className='form-control mb-2';
        contenedor.appendChild(input);
    });
    actualizar();
})();
</script>
@endif
</body></html>

@include('plantillas.encabezado')
<body>
<div class="preloader"><div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div></div>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
     data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
@include('plantillas.encabezado2')
@include('plantillas.menu')
<div class="page-wrapper">
  <div class="page-breadcrumb"><div class="container-fluid">
    <div class="cm-page-heading">
      <h4 class="page-title"><i class="fa-solid fa-file-lines me-2" aria-hidden="true"></i>Cartas</h4>
      <p class="cm-subtitle mb-0">Selecciona los reportes para preparar una carta. Próximamente: borradores y cartas emitidas.</p>
    </div>
  </div></div>
  <div class="container-fluid">
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
    <div class="row g-3 mb-3">
      <div class="col-md-6"><div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
        <i class="fa-regular fa-pen-to-square fa-2x text-primary" aria-hidden="true"></i>
        <div><h5 class="mb-1">Borradores</h5><p class="text-muted small mb-0">El guardado permanente estará disponible en la siguiente fase.</p></div>
      </div></div></div>
      <div class="col-md-6"><div class="card h-100"><div class="card-body d-flex align-items-center gap-3">
        <i class="fa-regular fa-file-pdf fa-2x text-primary" aria-hidden="true"></i>
        <div><h5 class="mb-1">Cartas emitidas</h5><p class="text-muted small mb-0">El archivo de documentos emitidos se activará en la siguiente fase.</p></div>
      </div></div></div>
    </div>
    <div class="card"><div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h5 class="mb-1">Reportes disponibles para cartas</h5>
        <small class="text-muted">Puedes elegir entre 1 y 30 reportes para una misma carta. Los datos originales permanecen intactos.</small></div>
        <span class="badge bg-primary">{{ $registros->count() }} visibles</span>
      </div>
      <form method="GET" action="{{ route('cartas.index') }}" class="row g-2 mb-3" autocomplete="off">
        <div class="col-md-7"><label class="form-label" for="buscarCartas">Buscar reporte o vecino</label>
          <input class="form-control" id="buscarCartas" name="buscar" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Título, vecino o número de reporte"></div>
        <div class="col-md-3"><label class="form-label" for="categoriaCartas">Categoría</label>
          <select class="form-select" id="categoriaCartas" name="categoria"><option value="">Todas</option>
          @foreach($categorias as $cat)<option value="{{ $cat->idCategoria }}" @selected((string)($filtros['categoria'] ?? '') === (string)$cat->idCategoria)>{{ $cat->nombre }}</option>@endforeach
          </select></div>
        <div class="col-md-2 d-flex align-items-end gap-2"><button class="btn btn-primary" type="submit">Filtrar</button>
          <a class="btn btn-outline-secondary" href="{{ route('cartas.index') }}">Limpiar</a></div>
      </form>
      <form method="GET" action="{{ route('documentos.preparar') }}" id="cmCartasSeleccion">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
          <button class="btn btn-primary" type="submit" id="cmPrepararCarta" disabled><i class="fa-solid fa-file-circle-plus me-1"></i> Preparar carta</button>
          <span class="text-muted small" id="cmSeleccionContador">Seleccionados: 0/30</span>
        </div>
        <div class="table-responsive">
          <table class="table cm-report-table align-middle">
            <thead><tr><th>Elegir</th><th>Reporte</th><th>Categoría</th><th>Estado</th><th>Vecino</th><th>Fecha</th><th>Acciones</th></tr></thead>
            <tbody>
            @forelse($registros as $r)
              <tr>
                <td><input type="checkbox" class="form-check-input cm-carta-check" name="reportes[]" value="{{ $r->idReporte }}" aria-label="Seleccionar reporte {{ $r->idReporte }}"></td>
                <td><strong>{{ $r->titulo }}</strong><div class="small text-muted">#{{ $r->idReporte }}</div></td>
                <td>{{ $r->categoria }}</td>
                <td><span class="badge {{ $r->estado === 'Pendiente' ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $r->estado }}</span></td>
                <td>{{ trim($r->usuario.' '.($r->apellido ?? '')) }}</td>
                <td class="small">{{ $r->fechaCreacion }}</td>
                <td><button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cmCartaDetalle{{ $r->idReporte }}" aria-label="Ver detalle del reporte {{ $r->idReporte }}"><i class="fa-solid fa-eye"></i> Detalle</button></td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted py-4">No hay reportes disponibles para los filtros seleccionados.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </form>
      <p class="small text-muted mb-0">Fase 1: se mantiene el editor de cartas que ya funciona. El expediente colectivo persistente y el historial documental se incorporarán después.</p>
    </div></div>
  </div>
  @include('plantillas.pie')
</div></div>
@foreach($registros as $r)
<div class="modal fade" id="cmCartaDetalle{{ $r->idReporte }}" tabindex="-1" aria-labelledby="cmCartaTitulo{{ $r->idReporte }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="cmCartaTitulo{{ $r->idReporte }}">Reporte #{{ $r->idReporte }} · {{ $r->titulo }}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
    <div class="modal-body">
      <dl class="row">
        <dt class="col-sm-3">Categoría</dt><dd class="col-sm-9">{{ $r->categoria }}</dd>
        <dt class="col-sm-3">Vecino</dt><dd class="col-sm-9">{{ trim($r->usuario.' '.($r->apellido ?? '')) }}</dd>
        <dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $r->estado }}</dd>
        <dt class="col-sm-3">Dirección</dt><dd class="col-sm-9">{{ $r->direccion ?: 'No especificada' }}</dd>
        <dt class="col-sm-3">Fecha</dt><dd class="col-sm-9">{{ $r->fechaCreacion }}</dd>
        <dt class="col-sm-3">Descripción</dt><dd class="col-sm-9" style="white-space:pre-wrap;overflow-wrap:anywhere">{{ $r->descripcion }}</dd>
      </dl>
      @if($r->imagen)<img src="{{ asset('storage/'.$r->imagen) }}" class="img-fluid rounded" alt="Fotografía original del reporte" style="max-height:260px;object-fit:contain">@endif
    </div>
    <div class="modal-footer"><a class="btn btn-outline-primary" href="{{ route('reportes.ver',['id'=>$r->idReporte]) }}">Ver reporte completo</a>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
  </div></div>
</div>
@endforeach
@include('plantillas.scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
  const cajas=[...document.querySelectorAll('.cm-carta-check')],boton=document.getElementById('cmPrepararCarta'),contador=document.getElementById('cmSeleccionContador');
  function actualizar(){
    const seleccionadas=cajas.filter(c=>c.checked);
    boton.disabled=seleccionadas.length===0 || seleccionadas.length>30;
    contador.textContent='Seleccionados: '+seleccionadas.length+'/30';
  }
  cajas.forEach(c=>c.addEventListener('change',function(){
    if(cajas.filter(x=>x.checked).length>30){c.checked=false;alert('Puedes seleccionar como máximo 30 reportes.');}
    actualizar();
  }));
  actualizar();
});
</script>
</body></html>

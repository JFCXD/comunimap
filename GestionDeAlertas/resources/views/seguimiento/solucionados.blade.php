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
      <h4 class="page-title"><i class="fa-solid fa-circle-check me-2" aria-hidden="true"></i>Solucionados</h4>
      <p class="cm-subtitle mb-0">Historial de reportes resueltos y evidencias existentes.</p>
    </div>
  </div></div>
  <div class="container-fluid">
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card"><div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h5 class="mb-1">Reportes solucionados</h5><small class="text-muted">Consulta de solo lectura; conserva el estado original de todos los reportes.</small></div>
        <span class="badge bg-success">{{ $registros->count() }} visibles</span>
      </div>
      <form method="GET" action="{{ route('solucionados.index') }}" autocomplete="off" class="row g-2 mb-3">
        <div class="col-md-7"><label for="cmSolBuscar" class="form-label">Buscar reporte o vecino</label>
          <input id="cmSolBuscar" class="form-control" name="buscar" maxlength="100" value="{{ $filtros['buscar'] ?? '' }}" placeholder="Título, vecino o número"></div>
        <div class="col-md-3"><label for="cmSolCategoria" class="form-label">Categoría</label>
          <select id="cmSolCategoria" class="form-select" name="categoria"><option value="">Todas</option>
          @foreach($categorias as $cat)<option value="{{ $cat->idCategoria }}" @selected((string)($filtros['categoria'] ?? '') === (string)$cat->idCategoria)>{{ $cat->nombre }}</option>@endforeach
          </select></div>
        <div class="col-md-2 d-flex align-items-end gap-2"><button class="btn btn-primary" type="submit">Filtrar</button>
          <a class="btn btn-outline-secondary" href="{{ route('solucionados.index') }}">Limpiar</a></div>
      </form>
      <div class="table-responsive"><table class="table cm-report-table align-middle">
        <thead><tr><th>Reporte</th><th>Categoría</th><th>Estado</th><th>Foto inicial</th><th>Vecino</th><th>Fecha del reporte</th><th>Acciones</th></tr></thead>
        <tbody>
        @forelse($registros as $r)
          <tr>
            <td><strong>{{ $r->titulo }}</strong><div class="small text-muted">#{{ $r->idReporte }}</div></td>
            <td>{{ $r->categoria }}</td>
            <td><span class="badge bg-success">Solucionado</span></td>
            <td>@if($r->imagen)<img src="{{ asset('storage/'.$r->imagen) }}" alt="Fotografía inicial del reporte" style="height:54px;width:65px;object-fit:cover;border-radius:7px">@else<span class="text-muted small">Sin foto</span>@endif</td>
            <td>{{ trim($r->usuario.' '.($r->apellido ?? '')) }}</td>
            <td class="small">{{ $r->fechaCreacion }}</td>
            <td><button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cmSolDetalle{{ $r->idReporte }}" aria-label="Ver detalle de solución del reporte {{ $r->idReporte }}"><i class="fa-solid fa-eye"></i> Detalle</button></td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted py-4">No hay reportes solucionados que coincidan con los filtros.</td></tr>
        @endforelse
        </tbody>
      </table></div>
      <div class="alert alert-info mb-0 mt-2 small">
        <i class="fa-solid fa-circle-info me-1" aria-hidden="true"></i>
        Los reportes resueltos anteriormente se muestran aunque todavía no tengan explicación de cierre ni fotografía posterior.
        Estas evidencias, los anexos y el historial se incorporarán en la siguiente fase.
      </div>
    </div></div>
  </div>
  @include('plantillas.pie')
</div></div>
@foreach($registros as $r)
<div class="modal fade" id="cmSolDetalle{{ $r->idReporte }}" tabindex="-1" aria-labelledby="cmSolTitulo{{ $r->idReporte }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="cmSolTitulo{{ $r->idReporte }}">Solucionado #{{ $r->idReporte }} · {{ $r->titulo }}</h5>
      <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
    <div class="modal-body">
      <dl class="row">
        <dt class="col-sm-3">Categoría</dt><dd class="col-sm-9">{{ $r->categoria }}</dd>
        <dt class="col-sm-3">Reportado por</dt><dd class="col-sm-9">{{ trim($r->usuario.' '.($r->apellido ?? '')) }}</dd>
        <dt class="col-sm-3">Estado</dt><dd class="col-sm-9">{{ $r->estado }}</dd>
        <dt class="col-sm-3">Dirección</dt><dd class="col-sm-9">{{ $r->direccion ?: 'No especificada' }}</dd>
        <dt class="col-sm-3">Fecha original</dt><dd class="col-sm-9">{{ $r->fechaCreacion }}</dd>
        <dt class="col-sm-3">Descripción</dt><dd class="col-sm-9" style="white-space:pre-wrap;overflow-wrap:anywhere">{{ $r->descripcion }}</dd>
      </dl>
      <div class="row g-3">
        <div class="col-md-6"><div class="border rounded p-3 h-100">
          <h6>Antes · Fotografía original</h6>
          @if($r->imagen)<img class="img-fluid rounded" src="{{ asset('storage/'.$r->imagen) }}" alt="Evidencia fotográfica original" style="max-height:250px;object-fit:contain">
          @else<p class="small text-muted mb-0">No se adjuntó fotografía inicial.</p>@endif
        </div></div>
        <div class="col-md-6"><div class="border rounded p-3 h-100">
          <h6>Después · Evidencia del cierre</h6>
          <p class="small text-muted mb-0">Aún no registrada. La fotografía posterior obligatoria se habilitará en una fase posterior y no se exigirá retroactivamente.</p>
        </div></div>
      </div>
      <div class="border rounded p-3 mt-3">
        <h6>Explicación, historial y anexos</h6>
        <p class="small text-muted mb-0">El sistema actual no almacena estos datos de cierre. Se integrarán sin alterar los registros anteriores.</p>
      </div>
    </div>
    <div class="modal-footer">
      <a class="btn btn-outline-primary" href="{{ route('reportes.ver',['id'=>$r->idReporte]) }}">Ver reporte original</a>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
    </div>
  </div></div>
</div>
@endforeach
@include('plantillas.scripts')
</body></html>

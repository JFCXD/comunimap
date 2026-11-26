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
    <h4 class="page-title"><i class="fa-solid fa-circle-check me-2"></i>Registrar solución {{ $tipo === 'grupo' ? 'colectiva' : 'individual' }}</h4>
    <p class="cm-subtitle mb-0">Documenta la atención antes de cerrar {{ $tipo === 'grupo' ? 'todos los reportes del caso' : 'el reporte' }}.</p>
   </div>
  </div></div>
  <div class="container-fluid">
   @if($errors->any())<div class="alert alert-danger" role="alert"><strong>No se pudo guardar:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
   <div class="row g-3">
    <div class="col-lg-5">
     <div class="card h-100"><div class="card-body">
      <h5 class="mb-2">Reporte(s) que se solucionarán</h5>
      <p class="text-muted small">{{ $reportes->count() }} reporte(s) · {{ $reportes->pluck('usuario')->unique()->count() }} vecino(s) diferente(s)</p>
      <div style="max-height:500px;overflow-y:auto">
      @foreach($reportes as $r)
       <div class="border rounded p-3 mb-2">
        <div class="fw-semibold">#{{ $r->idReporte }} · {{ $r->titulo }}</div>
        <div class="small text-muted mb-2">{{ $r->usuario }} · {{ $r->estado }}</div>
        @if($r->imagen)<img src="{{ asset('storage/'.$r->imagen) }}" alt="Fotografía original" class="img-fluid rounded" style="max-height:180px;object-fit:contain">@else
        <p class="text-muted small mb-0">Este reporte no tiene imagen inicial.</p>@endif
       </div>
      @endforeach
      </div>
     </div></div>
    </div>
    <div class="col-lg-7">
     <div class="card"><div class="card-body">
      <h5 class="mb-3">Pruebas de solución</h5>
      <form method="POST" action="{{ route('soluciones.guardar',['id'=>$reporte->idReporte]) }}" enctype="multipart/form-data" id="cmCerrarSolucion">
       @csrf
       <input type="hidden" name="tipo" value="{{ $tipo }}">
       <div class="mb-3">
        <label class="form-label fw-semibold" for="explicacionSolucion">¿Qué se solucionó y cómo? *</label>
        <textarea id="explicacionSolucion" class="form-control" name="explicacion" rows="6" maxlength="10000" minlength="15" required
         placeholder="Describe qué se hizo, dónde se realizó y cómo se comprobó la solución.">{{ old('explicacion') }}</textarea>
        <small class="text-muted">Mínimo 15 caracteres. La explicación será visible en Solucionados.</small>
       </div>
       <div class="mb-3">
        <label class="form-label" for="responsableSolucion">Responsable de la atención (opcional)</label>
        <input id="responsableSolucion" class="form-control" name="responsable" maxlength="180" value="{{ old('responsable') }}"
         placeholder="Por ejemplo: personal municipal o junta vecinal">
       </div>
       <div class="mb-3">
        <label class="form-label fw-semibold" for="fotoDespuesSolucion">Fotografía posterior obligatoria *</label>
        <input type="file" id="fotoDespuesSolucion" class="form-control" name="foto_despues" accept="image/jpeg,image/png,image/webp" required>
        <small class="text-muted">JPG, PNG o WEBP. Máximo 6 MB.</small>
       </div>
       <div class="mb-3">
        <label class="form-label" for="anexosSolucion">Anexos adicionales (opcional)</label>
        <input type="file" id="anexosSolucion" class="form-control" name="anexos[]" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple>
        <small class="text-muted">Hasta 5 archivos JPG, PNG, WEBP o PDF, de hasta 6 MB cada uno.</small>
       </div>
       <div class="alert alert-info small">
        <i class="fa-solid fa-circle-info me-1"></i>
        Al confirmar, se guardará una sola solución {{ $tipo === 'grupo' ? 'para este grupo' : 'para este reporte' }},
        se actualizará el estado a Solucionado y se enviarán los avisos habituales. No podrás borrar la evidencia desde esta pantalla.
       </div>
       <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-success" type="submit" onclick="return confirm('¿Confirmas el cierre definitivo con esta evidencia?')">
         <i class="fa-solid fa-check me-1"></i>Guardar evidencias y solucionar
        </button>
        <a class="btn btn-outline-secondary" href="{{ url('/reportes') }}">Cancelar</a>
       </div>
      </form>
     </div></div>
    </div>
   </div>
  </div>
  @include('plantillas.pie')
 </div>
</div>
@include('plantillas.scripts')
</body>
</html>

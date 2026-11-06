@include('plantillas.encabezado')
<body>
<div class="preloader"><div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div></div>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
     data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    @include('plantillas.encabezado2')
    @include('plantillas.menu')
    <div class="page-wrapper">
        <div class="page-breadcrumb">
            <div class="container-fluid">
                <h4 class="page-title">Detalle del reporte #{{ $reporte->idReporte }}</h4>
                <p class="cm-subtitle">Consulta de información, sin modificar el reporte.</p>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card"><div class="card-body">
                <h4 class="mb-3">{{ $reporte->titulo }}</h4>
                <div class="row g-3">
                    <div class="col-md-7">
                        <p><strong>Categoría:</strong> {{ $reporte->categoria }}</p>
                        <p><strong>Estado:</strong> {{ $reporte->estado }}</p>
                        <p><strong>Registrado por:</strong> {{ $reporte->usuario }}</p>
                        <p><strong>Fecha:</strong> {{ $reporte->fechaCreacion }}</p>
                        <p><strong>Dirección:</strong> {{ $reporte->direccion ?: 'No especificada' }}</p>
                        <h5>Descripción del problema</h5>
                        <p style="white-space:pre-wrap;overflow-wrap:anywhere;">{{ $reporte->descripcion }}</p>
                    </div>
                    @if($reporte->imagen)
                        <div class="col-md-5">
                            <img src="{{ asset('storage/' . $reporte->imagen) }}" alt="Fotografía del reporte"
                                 class="img-fluid rounded" style="max-height:320px;object-fit:contain;">
                        </div>
                    @endif
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @if($reporte->ubicacion && count(explode(',', $reporte->ubicacion)) === 2)
                        <a href="{{ url('/inicio') }}?reporte={{ $reporte->idReporte }}#mapa"
                           class="btn btn-primary">
                            <i class="fa-solid fa-location-dot me-1" aria-hidden="true"></i>
                            Mostrar ubicación en el mapa
                        </a>
                    @else
                        <span class="text-muted">Este reporte no tiene una ubicación válida para mostrar en el mapa.</span>
                    @endif
                    <a href="{{ url('/reportes/pdf/' . $reporte->idReporte) }}" target="_blank"
                       rel="noopener" class="btn btn-outline-primary">
                        <i class="fa-solid fa-file-pdf me-1" aria-hidden="true"></i> Ver PDF
                    </a>
                    <a href="{{ url('/reportes') }}" class="btn btn-outline-secondary">Volver a reportes</a>
                </div>
            </div></div>
        </div>
        @include('plantillas.pie')
    </div>
</div>
@include('plantillas.scripts')
</body>
</html>

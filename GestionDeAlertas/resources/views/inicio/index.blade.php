@include('plantillas.encabezado')

<body>

    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">

        @include('plantillas.encabezado2')
        @include('plantillas.menu')

        <div class="page-wrapper">

            <div class="page-breadcrumb">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="cm-page-heading">
                            <h4 class="page-title">{{ session('rol') == 'Administrador' ? 'Panel de inicio' : 'Bienvenido a ComuniMap' }}</h4>
                            <p class="cm-subtitle mb-0">{{ session('rol') == 'Administrador' ? 'Consulta las incidencias y administra el barrio.' : 'Consulta los reportes de tu barrio y comunica cualquier problema.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-lg-8">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title mb-1"><i class="fas fa-map-marked-alt text-primary me-2" aria-hidden="true"></i>Mapa de reportes de la OTB</h4>
                                <p class="cm-subtitle mb-3">Reportes registrados y perímetro del barrio</p>
                                <div id="mapa" class="cm-overview-map"></div>
                                <div class="cm-legend mt-3" aria-label="Leyenda de los estados">
                                    <span><i class="cm-legend-dot" style="background:#2563eb"></i>Límite OTB</span>
                                    <span><i class="cm-legend-dot" style="background:#dc3545"></i>Pendiente</span>
                                    <span><i class="cm-legend-dot" style="background:#f4b52b"></i>En proceso</span>
                                    <span><i class="cm-legend-dot" style="background:#198754"></i>Solucionado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h4 class="card-title mb-1"><i class="fas fa-bullhorn text-primary me-2" aria-hidden="true"></i>Última publicación</h4>
                                <p class="cm-subtitle mb-3">Novedades de la OTB Universidad Alto</p>
                                @if($ultimaPublicacion)
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="badge bg-primary">{{ $ultimaPublicacion->tipo }}</span>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($ultimaPublicacion->fechaCreacion)->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <h5 class="mb-2">{{ $ultimaPublicacion->titulo }}</h5>
                                    <p class="text-muted mb-3" style="overflow-wrap:anywhere;">{{ \Illuminate\Support\Str::limit($ultimaPublicacion->descripcion, 180) }}</p>
                                    @if($ultimaPublicacion->tipo === 'Actividad')
                                        <div class="small text-muted mb-3">
                                            <div><i class="far fa-calendar-alt me-1"></i> {{ $ultimaPublicacion->fechaActividad ? \Carbon\Carbon::parse($ultimaPublicacion->fechaActividad)->format('d/m/Y') : 'Fecha por definir' }}</div>
                                            @if($ultimaPublicacion->lugarActividad)<div><i class="fas fa-location-dot me-1"></i> {{ $ultimaPublicacion->lugarActividad }}</div>@endif
                                        </div>
                                    @elseif($ultimaPublicacion->tipo === 'Encuesta')
                                        <p class="small text-primary mb-3"><i class="fas fa-poll me-1"></i> Consulta la encuesta en Comunidad</p>
                                    @endif
                                    <a class="btn btn-primary mt-auto" href="{{ route('comunidad.index', ['publicacion' => $ultimaPublicacion->idPublicacion]) }}#publicacion-{{ $ultimaPublicacion->idPublicacion }}">
                                        Ver publicación <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i>
                                    </a>
                                @else
                                    <p class="text-muted mb-3">Todavía no hay publicaciones de la OTB.</p>
                                    <a class="btn btn-outline-primary mt-auto" href="{{ route('comunidad.index') }}">Ir a Comunidad</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('plantillas.pie')
        </div>
    </div>

    @include('plantillas.scripts')
    <script>
        // Crear mapa
        var mapa = L.map('mapa').setView([-17.438828, -66.142760], 16);
        // Capa base
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapa);
        // Límite territorial: el mismo GeoJSON utilizado por la API de ComuniMap.
        fetch("{{ asset('otb_limite.geojson') }}")
            .then(function(respuesta) {
                if (!respuesta.ok) throw new Error('No se pudo cargar el límite de la OTB');
                return respuesta.json();
            })
            .then(function(limite) {
                var capaOTB = L.geoJSON(limite, {
                    style: { color: '#2563eb', weight: 4, fillColor: '#3b82f6', fillOpacity: 0.15 },
                    interactive: false
                }).addTo(mapa);
                mapa.fitBounds(capaOTB.getBounds(), { padding: [24, 24] });
            })
            .catch(function(error) { console.error('ComuniMap OTB:', error); });
        // Iconos
        var iconoRojo = new L.Icon({
            iconUrl: "{{ asset('images/marker-icon-red.png') }}",
            shadowUrl: "{{ asset('images/marker-shadow.png') }}",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        var iconoAmarillo = new L.Icon({
            iconUrl: "{{ asset('images/marker-icon-gold.png') }}",
            shadowUrl: "{{ asset('images/marker-shadow.png') }}",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        var iconoVerde = new L.Icon({
            iconUrl: "{{ asset('images/marker-icon-green.png') }}",
            shadowUrl: "{{ asset('images/marker-shadow.png') }}",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        @foreach($reportes as $reporte)
                    var punto = "{{ $reporte->ubicacion }}".split(",");
                    var icono = iconoRojo;
                    if ("{{ $reporte->estado }}" == "En Proceso") {
                        icono = iconoAmarillo;
                    }
                    if ("{{ $reporte->estado }}" == "Solucionado") {
                        icono = iconoVerde;
                    }
                    L.marker([punto[0], punto[1]], {
                        icon: icono
                    })
                        .addTo(mapa)
                        .bindPopup(`
            <div style="
                width:230px;
                font-family:Arial,sans-serif;
            ">

                @if($reporte->imagen)
                    <img
                        src="{{ asset('storage/' . $reporte->imagen) }}"
                        style="
                            width:100%;
                            height:130px;
                            object-fit:cover;
                            border-radius:8px;
                            margin-bottom:8px;
                        ">
                @endif
                <a href="/reportes/pdf/{{ $reporte->idReporte }}"
                    target="_blank"
                    style="
                        text-decoration:none;
                        color:#0d6efd;
                        font-size:16px;
                        font-weight:bold;">
                    {{ $reporte->titulo }}
                </a>
                <hr style="margin:8px 0;">
                <div style="font-size:13px;">
                    <b>Categoría</b><br>
                    {{ $reporte->categoria }}
                </div>
                <div style="margin-top:6px;font-size:13px;">
                    <b>Dirección</b><br>
                    {{ $reporte->direccion }}
                </div>
                <div style="margin-top:10px;">
                    <span style="
                        padding:4px 10px;
                        border-radius:20px;
                        color:white;
                        font-size:11px;
                        font-weight:bold;
                        background:
                        @if($reporte->estado == 'Pendiente')
                            #dc3545
                        @elseif($reporte->estado == 'En Proceso')
                            #ffc107
                        @else
                            #198754
                        @endif;
                    ">
                        {{ $reporte->estado }}
                    </span>
                </div>

            </div>
            `);
        @endforeach
    </script>
</body>

</html>
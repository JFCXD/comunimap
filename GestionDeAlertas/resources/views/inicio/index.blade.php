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
                        <h4 class="page-title">{{ session('rol') == 'Administrador' ? 'Panel de inicio' : 'Bienvenido a ComuniMap' }}</h4>
                        <p class="cm-subtitle mb-0">{{ session('rol') == 'Administrador' ? 'Consulta las incidencias y administra el barrio.' : 'Consulta los reportes de tu barrio y comunica cualquier problema.' }}</p>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <a class="cm-quicklink" href="/alertas">
                                    <span class="cm-quicklink-icon" style="background:#fff0e9;color:#dc5e35;"><i class="fas fa-plus-circle" aria-hidden="true"></i></span>
                                    <span><strong>Registrar alerta</strong><small>Informar un problema dentro de la OTB</small></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <a class="cm-quicklink" href="/reportes">
                                    <span class="cm-quicklink-icon"><i class="fas fa-clipboard-list" aria-hidden="true"></i></span>
                                    <span><strong>{{ session('rol') == 'Administrador' ? 'Consultar reportes' : 'Mis reportes' }}</strong><small>{{ session('rol') == 'Administrador' ? 'Revisar las incidencias registradas' : 'Consultar el seguimiento de tus alertas' }}</small></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @if(session('rol') == 'Administrador')
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <a class="cm-quicklink" href="/usuarios">
                                    <span class="cm-quicklink-icon" style="background:#e8f8ef;color:#16834a;"><i class="fas fa-users" aria-hidden="true"></i></span>
                                    <span><strong>Usuarios</strong><small>Revisar y aprobar solicitudes de registro</small></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3" style="gap:12px;">
                                    <div>
                                        <h4 class="card-title mb-1">Mapa de reportes de la OTB</h4>
                                        <p class="cm-subtitle mb-0">Reportes registrados y perímetro del barrio</p>
                                    </div>
                                    <div class="cm-legend" aria-label="Leyenda de los estados">
                                        <span><i class="cm-legend-dot" style="background:#2563eb"></i>Límite OTB</span>
                                        <span><i class="cm-legend-dot" style="background:#dc3545"></i>Pendiente</span>
                                        <span><i class="cm-legend-dot" style="background:#f4b52b"></i>En proceso</span>
                                        <span><i class="cm-legend-dot" style="background:#198754"></i>Solucionado</span>
                                    </div>
                                </div>
                                <div id="mapa" style="height:600px;max-height:70vh;min-height:380px;border-radius:12px;overflow:hidden;"></div>
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
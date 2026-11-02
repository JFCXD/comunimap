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

            <div class="page-breadcrumb bg-white">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">INICIO</h4>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">
                                    Mapa de Alertas
                                </h4>
                                <div id="mapa" style="height:600px;border-radius:10px;">
                                </div>
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
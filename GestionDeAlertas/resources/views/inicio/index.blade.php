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

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Crear mapa
        var mapa = L.map('mapa').setView([-17.3935, -66.1570], 13);
        // Capa base
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapa);
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
    @include('plantillas.scripts')
</body>

</html>
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
                    <div class="col-md-6">
                        <h4 class="page-title">ALERTAS</h4>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <!-- MAPA -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div id="mapa" style="height:550px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- FORMULARIO -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">
                                    Registrar Alerta
                                </h4>
                                <form action="/alertas" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label>Título</label>
                                        <input type="text" name="titulo" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Descripción</label>
                                        <textarea name="descripcion" class="form-control" rows="4" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label>Dirección</label>
                                        <input type="text" name="direccion" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Categoría</label>
                                        <select name="idCategoria" class="form-control" required>
                                            <option value="">Seleccione una categoría</option>
                                            <option value="1">Basura</option>
                                            <option value="2">Alumbrado Público</option>
                                            <option value="3">Calles</option>
                                            <option value="4">Áreas Verdes</option>
                                            <option value="5">Seguridad</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label>Foto del problema</label>
                                        <input type="file" name="imagen" class="form-control" accept="image/*">
                                    </div>
                                    <div class="mb-3">
                                        <label>Ubicación</label>
                                        <input type="text" id="ubicacion" name="ubicacion" class="form-control"
                                            readonly>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100">
                                        Registrar Alerta
                                    </button>
                                </form>
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
        // Centro inicial (Cochabamba)
        var mapa = L.map('mapa').setView([-17.3935, -66.1570], 13);
        // OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapa);
        // Marcador
        var marcador;
        mapa.on('click', function (e) {
            if (marcador) {
                mapa.removeLayer(marcador);
            }
            marcador = L.marker(e.latlng).addTo(mapa);
            document.getElementById("ubicacion").value =
                e.latlng.lat + "," + e.latlng.lng;
        });
    </script>
</body>

</html>
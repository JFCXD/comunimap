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
                                        @error('ubicacion')
                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                        @enderror
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
        var mapa = L.map('mapa').setView([-17.438828, -66.142760], 16);
        // OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapa);
        // Geocerca de la OTB. Mientras no cargue, no se permite seleccionar un punto.
        var limiteOTB = null;
        var ubicacionValida = false;
        var botonGuardar = document.querySelector('form[action="/alertas"] button[type="submit"]');
        var campoUbicacion = document.getElementById('ubicacion');
        var mensajeOTB = document.createElement('div');
        mensajeOTB.className = 'text-danger small mt-2';
        campoUbicacion.insertAdjacentElement('afterend', mensajeOTB);
        if (botonGuardar) botonGuardar.disabled = true;

        function puntoEnAnillo(lat, lng, anillo) {
            var dentro = false;
            for (var i = 0, j = anillo.length - 1; i < anillo.length; j = i++) {
                var ax = anillo[i][0], ay = anillo[i][1];
                var bx = anillo[j][0], by = anillo[j][1];
                var cruz = (lng - ax) * (by - ay) - (lat - ay) * (bx - ax);
                if (Math.abs(cruz) <= 1e-11 &&
                    lng >= Math.min(ax, bx) - 1e-10 && lng <= Math.max(ax, bx) + 1e-10 &&
                    lat >= Math.min(ay, by) - 1e-10 && lat <= Math.max(ay, by) + 1e-10) {
                    return true;
                }
                if ((ay > lat) !== (by > lat)) {
                    var cruce = ax + (lat - ay) * (bx - ax) / (by - ay);
                    if (lng < cruce) dentro = !dentro;
                }
            }
            return dentro;
        }

        fetch("{{ asset('otb_limite.geojson') }}")
            .then(function(respuesta) {
                if (!respuesta.ok) throw new Error('No se pudo cargar el límite');
                return respuesta.json();
            })
            .then(function(geojson) {
                var anillo = geojson.features[0].geometry.coordinates[0];
                if (!Array.isArray(anillo) || anillo.length < 4) throw new Error('Polígono inválido');
                limiteOTB = anillo;
                var poligono = L.geoJSON(geojson, {
                    style: { color: '#2563eb', weight: 4, fillColor: '#3b82f6', fillOpacity: 0.15 },
                    interactive: false
                }).addTo(mapa);
                mapa.fitBounds(poligono.getBounds(), { padding: [24, 24] });
            })
            .catch(function(error) {
                mensajeOTB.textContent = 'No se pudieron cargar los límites de la OTB. Actualiza la página.';
                console.error('ComuniMap OTB:', error);
            });

        // Marcador
        var marcador;
        mapa.on('click', function (e) {
            var dentro = limiteOTB && puntoEnAnillo(e.latlng.lat, e.latlng.lng, limiteOTB);
            if (!dentro) {
                ubicacionValida = false;
                campoUbicacion.value = '';
                if (botonGuardar) botonGuardar.disabled = true;
                if (marcador) { mapa.removeLayer(marcador); marcador = null; }
                mensajeOTB.textContent = limiteOTB
                    ? 'Esta ubicación está fuera de los límites de la OTB. Selecciona un punto dentro del barrio.'
                    : 'No se han cargado los límites de la OTB.';
                return;
            }
            if (marcador) mapa.removeLayer(marcador);
            marcador = L.marker(e.latlng).addTo(mapa);
            campoUbicacion.value = e.latlng.lat + ',' + e.latlng.lng;
            ubicacionValida = true;
            mensajeOTB.textContent = '';
            if (botonGuardar) botonGuardar.disabled = false;
        });
    </script>
</body>

</html>
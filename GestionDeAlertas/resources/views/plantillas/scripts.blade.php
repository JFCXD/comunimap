<script src="{{ asset('ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>

<script src="{{ asset('ample/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('ample/js/app-style-switcher.js') }}"></script>

<script src="{{ asset('ample/plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

<script src="{{ asset('ample/js/waves.js') }}"></script>

<script src="{{ asset('ample/js/sidebarmenu.js') }}"></script>

<script src="{{ asset('ample/js/custom.js') }}"></script>

<script src="{{ asset('ample/plugins/bower_components/chartist/dist/chartist.min.js') }}"></script>

<script
    src="{{ asset('ample/plugins/bower_components/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>

<script src="{{ asset('ample/js/pages/dashboards/dashboard1.js') }}"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>

    // Crear el mapa

    var mapa = L.map('mapa').setView([-17.3935, -66.1570], 13);

    // OpenStreetMap

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapa);

    var marcador;

    // Obtener la ubicación actual

    if (navigator.geolocation) {

        navigator.geolocation.getCurrentPosition(function(posicion) {

            var lat = posicion.coords.latitude;
            var lng = posicion.coords.longitude;

            mapa.setView([lat, lng], 17);

            marcador = L.marker([lat, lng]).addTo(mapa);

            document.getElementById("ubicacion").value = lat + "," + lng;

        }, function() {

            alert("No se pudo obtener la ubicación.");

        });

    }

    // Al hacer clic en el mapa

    mapa.on('click', function(e){

        if(marcador){
            mapa.removeLayer(marcador);
        }

        marcador = L.marker(e.latlng).addTo(mapa);

        document.getElementById("ubicacion").value =
            e.latlng.lat + "," + e.latlng.lng;

    });

</script>
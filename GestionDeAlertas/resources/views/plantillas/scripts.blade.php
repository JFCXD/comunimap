<script src="{{ secure_asset('ample/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>

<script src="{{ secure_asset('ample/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ secure_asset('ample/js/app-style-switcher.js') }}"></script>

<script src="{{ secure_asset('ample/plugins/bower_components/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

<script src="{{ secure_asset('ample/js/waves.js') }}"></script>

<script src="{{ secure_asset('ample/js/sidebarmenu.js') }}"></script>

<script src="{{ secure_asset('ample/js/custom.js') }}"></script>

<script src="{{ secure_asset('ample/plugins/bower_components/chartist/dist/chartist.min.js') }}"></script>

<script
    src="{{ secure_asset('ample/plugins/bower_components/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>

<script src="{{ secure_asset('ample/js/pages/dashboards/dashboard1.js') }}"></script>
<!-- Cerrar el menú móvil al tocar fuera de él, sin modificar Ample. -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var contenedor = document.getElementById('main-wrapper');
    if (!contenedor) return;
    document.addEventListener('click', function (evento) {
        if (window.innerWidth > 767 || !contenedor.classList.contains('show-sidebar')) return;
        if (evento.target.closest('.left-sidebar') || evento.target.closest('.nav-toggler')) return;
        contenedor.classList.remove('show-sidebar');
        var icono = document.querySelector('.nav-toggler i');
        if (icono) icono.classList.add('ti-menu');
    });
});
</script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
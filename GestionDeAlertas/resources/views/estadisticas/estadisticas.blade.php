@include('plantillas.encabezado')
<body>
<div class="preloader"><div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div></div>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
@include('plantillas.encabezado2')
@include('plantillas.menu')
<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="cm-page-heading">
            <span class="stats-eyebrow"><i class="bi bi-activity"></i> PANEL DE ANÁLISIS COMUNITARIO</span>
            <h4 class="page-title">Estadísticas de ComuniMap</h4>
            <p class="estadisticas-subtitulo">Una visión clara de las incidencias y la respuesta comunitaria.</p>
        </div>
    </div>
    <div class="container-fluid">
        <section class="stats-hero" aria-label="Resumen del panel">
            <div>
                <span class="stats-hero-label">GESTIÓN DE INCIDENCIAS</span>
                <h2>Decisiones respaldadas por datos del barrio.</h2>
                <p>Explora tendencias, identifica las categorías más reportadas y revisa el avance de atención.</p>
            </div>
            <div class="stats-hero-number"><strong>{{ number_format($porcentajeResolucion, 1) }}%</strong><span>Resolución de reportes filtrados</span></div>
        </section>

        <section class="stats-filter-panel" aria-label="Filtros de estadísticas">
            <div class="stats-section-heading"><div><h3><i class="bi bi-sliders"></i> Filtros</h3><p>Los indicadores y gráficos de reportes se actualizan al aplicar los filtros.</p></div></div>
            <form method="GET" action="{{ url('/estadisticas') }}" class="stats-filter-grid">
                <label>Desde<input class="form-control" type="date" name="desde" value="{{ $desde }}"></label>
                <label>Hasta<input class="form-control" type="date" name="hasta" value="{{ $hasta }}"></label>
                <label>Categoría
                    <select class="form-control" name="categoria">
                        <option value="">Todas</option>
                        @foreach($categoriasDisponibles as $opcion)
                            <option value="{{ $opcion->idCategoria }}" @selected((string)$categoriaSeleccionada === (string)$opcion->idCategoria)>{{ $opcion->nombre }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Estado
                    <select class="form-control" name="estado">
                        <option value="">Todos</option>
                        @foreach(['Pendiente', 'En Proceso', 'Solucionado'] as $opcionEstado)
                            <option value="{{ $opcionEstado }}" @selected($estadoSeleccionado === $opcionEstado)>{{ $opcionEstado }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="stats-filter-actions"><button class="btn stats-btn-primary" type="submit"><i class="bi bi-funnel"></i> Aplicar</button><a class="btn stats-btn-clear" href="{{ url('/estadisticas') }}">Limpiar</a></div>
            </form>
            @if($errors->any())
                <div class="alert alert-danger mt-3" role="alert">{{ $errors->first() }}</div>
            @endif
        </section>

        <section class="stats-metrics" aria-label="Indicadores principales">
            <article class="stats-metric"><span class="stats-metric-icon blue"><i class="bi bi-file-earmark-bar-graph"></i></span><span class="stats-metric-label">Reportes</span><strong>{{ number_format($totalReportes) }}</strong><small>Según los filtros</small></article>
            <article class="stats-metric"><span class="stats-metric-icon amber"><i class="bi bi-hourglass-split"></i></span><span class="stats-metric-label">Pendientes</span><strong>{{ number_format($pendientes) }}</strong><small>Por atender</small></article>
            <article class="stats-metric"><span class="stats-metric-icon cyan"><i class="bi bi-arrow-repeat"></i></span><span class="stats-metric-label">En proceso</span><strong>{{ number_format($enProceso) }}</strong><small>En atención</small></article>
            <article class="stats-metric"><span class="stats-metric-icon green"><i class="bi bi-check2-circle"></i></span><span class="stats-metric-label">Solucionados</span><strong>{{ number_format($solucionados) }}</strong><small>Atendidos</small></article>
            <article class="stats-metric"><span class="stats-metric-icon purple"><i class="bi bi-people"></i></span><span class="stats-metric-label">Usuarios</span><strong>{{ number_format($totalUsuarios) }}</strong><small>Total registrado (sin filtros)</small></article>
        </section>

        <section class="stats-charts-grid">
            <article class="stats-panel stats-panel-wide">
                <div class="stats-section-heading"><div><h3>Evolución de reportes</h3><p>Hasta los últimos 12 meses con actividad dentro del período seleccionado.</p></div><span class="stats-heading-icon"><i class="bi bi-graph-up-arrow"></i></span></div>
                <div class="stats-chart-box"><canvas id="graficoEvolucion" aria-label="Reportes por mes" role="img"></canvas></div>
            </article>
            <article class="stats-panel">
                <div class="stats-section-heading"><div><h3>Distribución por estado</h3><p>Situación de los reportes filtrados.</p></div><span class="stats-heading-icon"><i class="bi bi-pie-chart"></i></span></div>
                <div class="stats-chart-box"><canvas id="graficoEstado" aria-label="Distribución por estado" role="img"></canvas></div>
            </article>
            <article class="stats-panel">
                <div class="stats-section-heading"><div><h3>Problemas por categoría</h3><p>Cantidad de reportes por tipo de incidencia.</p></div><span class="stats-heading-icon"><i class="bi bi-bar-chart"></i></span></div>
                <div class="stats-category-list">
                    @forelse($categorias as $categoria)
                        <div class="stats-category-row">
                            <div class="stats-category-title"><span>{{ $categoria->categoria }}</span><strong>{{ number_format($categoria->total) }}</strong></div>
                            <div class="stats-category-track"><div class="stats-category-fill" style="width: {{ $totalReportes > 0 ? min(100, round($categoria->total * 100 / $totalReportes, 2)) : 0 }}%"></div></div>
                        </div>
                    @empty
                        <p class="stats-empty">No hay categorías disponibles.</p>
                    @endforelse
                </div>
            </article>
        </section>
        @if($totalReportes === 0)
            <div class="stats-empty-banner"><i class="bi bi-info-circle"></i> No se encontraron reportes con los filtros seleccionados. Prueba otro período, categoría o estado.</div>
        @endif
        <div class="stats-footer-note"><i class="bi bi-shield-check"></i> Datos obtenidos de MySQL. Los indicadores de reportes respetan los filtros; el total de usuarios corresponde a todo el sistema. @if($ultimoReporte) Último reporte del conjunto: {{ $ultimoReporte }}. @endif</div>
    </div>
    @include('plantillas.pie')
</div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ secure_asset('css/estilosextra.css') }}">
<style>
.stats-eyebrow{display:inline-block;font-size:.72rem;font-weight:800;letter-spacing:.14em;color:#2563eb;margin-bottom:8px}
.stats-hero{display:flex;align-items:center;justify-content:space-between;gap:30px;padding:32px 36px;border-radius:22px;background:linear-gradient(115deg,#0b1730,#17376d 65%,#2563eb);color:#fff;margin-bottom:24px;box-shadow:0 14px 35px rgba(20,45,92,.13)}
.stats-hero h2{color:#fff;font-weight:800;font-size:clamp(1.4rem,2.6vw,2.15rem);max-width:610px;margin:10px 0}
.stats-hero p{color:#cbdcf9;margin:0;max-width:650px}
.stats-hero-label{font-size:.7rem;letter-spacing:.17em;font-weight:800;color:#a9c8ff}
.stats-hero-number{text-align:center;min-width:180px;border-left:1px solid #ffffff3d;padding-left:28px}
.stats-hero-number strong{display:block;font-size:2.6rem;line-height:1.2;color:#fff}
.stats-hero-number span{display:block;font-size:.8rem;color:#dbeafe}
.stats-filter-panel,.stats-panel,.stats-metric{background:#fff;border:1px solid #e6edf5;border-radius:18px;box-shadow:0 5px 20px rgba(22,42,75,.04)}
.stats-filter-panel{padding:24px;margin-bottom:22px}
.stats-section-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:20px}
.stats-section-heading h3{font-size:1.05rem;font-weight:800;color:#162d50;margin:0 0 5px}
.stats-section-heading p{font-size:.81rem;color:#64748b;margin:0}
.stats-filter-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr)) auto;gap:12px;align-items:end}
.stats-filter-grid label{font-size:.78rem;font-weight:700;color:#425672;margin:0}
.stats-filter-grid .form-control{margin-top:7px;height:42px;border:1px solid #dbe5f0;border-radius:10px;font-size:.83rem}
.stats-filter-actions{display:flex;gap:8px}
.stats-btn-primary{background:#2563eb;color:#fff;border-radius:10px;white-space:nowrap}
.stats-btn-primary:hover{background:#1d4ed8;color:#fff}
.stats-btn-clear{border:1px solid #dbe5f0;color:#475569;border-radius:10px;white-space:nowrap}
.stats-metrics{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-bottom:22px}
.stats-metric{padding:19px;display:flex;flex-direction:column;gap:7px;min-width:0}
.stats-metric-icon{width:38px;height:38px;border-radius:11px;display:grid;place-items:center;font-size:1.05rem}
.stats-metric-icon.blue{color:#2563eb;background:#eff6ff}.stats-metric-icon.amber{color:#b45309;background:#fffbeb}.stats-metric-icon.cyan{color:#0891b2;background:#ecfeff}.stats-metric-icon.green{color:#15803d;background:#f0fdf4}.stats-metric-icon.purple{color:#7c3aed;background:#f5f3ff}
.stats-metric-label{color:#64748b;font-size:.82rem;font-weight:700}
.stats-metric strong{color:#152b4a;font-size:2rem;line-height:1.1}
.stats-metric small{color:#94a3b8;font-size:.73rem}
.stats-charts-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-bottom:20px}
.stats-panel{padding:23px;min-width:0}
.stats-panel-wide{grid-column:1/-1}
.stats-heading-icon{width:38px;height:38px;border-radius:12px;background:#eff6ff;color:#2563eb;display:grid;place-items:center}
.stats-chart-box{position:relative;height:285px}
.stats-category-list{max-height:300px;overflow:auto;padding-right:4px}
.stats-category-row{margin-bottom:17px}
.stats-category-title{display:flex;justify-content:space-between;gap:10px;font-size:.82rem;color:#334155;margin-bottom:8px}
.stats-category-title strong{color:#152b4a}
.stats-category-track{height:9px;background:#eaf0f7;border-radius:20px;overflow:hidden}
.stats-category-fill{height:100%;background:linear-gradient(90deg,#60a5fa,#2563eb);border-radius:20px}
.stats-empty,.stats-empty-banner,.stats-footer-note{color:#64748b;font-size:.83rem}
.stats-empty-banner{padding:16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;margin-bottom:15px}
.stats-footer-note{padding:12px 0 22px}
@media(max-width:1200px){.stats-filter-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.stats-filter-actions{grid-column:1/-1}.stats-metrics{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:767px){.stats-hero{flex-direction:column;align-items:flex-start;padding:24px}.stats-hero-number{border-left:0;border-top:1px solid #ffffff3d;padding:18px 0 0;text-align:left;width:100%}.stats-filter-grid{grid-template-columns:1fr}.stats-filter-actions{grid-column:auto}.stats-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.stats-charts-grid{grid-template-columns:1fr}.stats-panel-wide{grid-column:auto}.stats-chart-box{height:240px}}
@media(max-width:420px){.stats-metrics{grid-template-columns:1fr}}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    if (typeof Chart === 'undefined') return;
    const evolution = @json($evolucion);
    const states = [{{ $pendientes }}, {{ $enProceso }}, {{ $solucionados }}, {{ $otrosEstados }}];
    const common = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };
    const lineCanvas = document.getElementById('graficoEvolucion');
    if (lineCanvas) new Chart(lineCanvas, {
        type: 'line',
        data: {
            labels: evolution.map(item => item.periodo),
            datasets: [{ label: 'Reportes', data: evolution.map(item => Number(item.total)), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.12)', fill: true, tension: .35, pointRadius: 4, pointBackgroundColor: '#2563eb' }]
        },
        options: { ...common, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }, plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } } }
    });
    const stateCanvas = document.getElementById('graficoEstado');
    if (stateCanvas) new Chart(stateCanvas, {
        type: 'doughnut',
        data: { labels: ['Pendientes','En proceso','Solucionados','Otros estados'], datasets: [{ data: states, backgroundColor: ['#f59e0b','#06b6d4','#22c55e','#94a3b8'], borderWidth: 3, borderColor: '#fff', hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14 } } } }
    });
})();
</script>
@include('plantillas.scripts')
</body>
</html>

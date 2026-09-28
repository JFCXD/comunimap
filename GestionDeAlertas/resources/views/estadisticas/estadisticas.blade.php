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

            <!-- =========================
                 ENCABEZADO
            ========================== -->

            <div class="page-breadcrumb bg-white">

                <div class="row align-items-center">

                    <div class="col-lg-6">

                        <h4 class="page-title">
                            ESTADÍSTICAS
                        </h4>

                        <p class="estadisticas-subtitulo">
                            Resumen general del sistema
                        </p>

                    </div>

                </div>

            </div>


            <div class="container-fluid">


                <!-- =========================
                     5 TARJETAS
                ========================== -->

                <div class="estadisticas-resumen">


                    <!-- USUARIOS -->

                    <div class="stat-card">

                        <div class="stat-icon azul">
                            <i class="bi bi-people-fill"></i>
                        </div>

                        <div class="stat-info">

                            <span>Usuarios</span>

                            <h2>
                                {{ $usuarios[0]->total }}
                            </h2>

                            <small>
                                Registrados
                            </small>

                        </div>

                    </div>


                    <!-- REPORTES -->

                    <div class="stat-card">

                        <div class="stat-icon azul">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>

                        <div class="stat-info">

                            <span>Total de reportes</span>

                            <h2>
                                {{ $reportes[0]->total }}
                            </h2>

                            <small>
                                Registrados
                            </small>

                        </div>

                    </div>


                    <!-- PENDIENTES -->

                    <div class="stat-card">

                        <div class="stat-icon rojo">
                            <i class="bi bi-exclamation-circle-fill"></i>
                        </div>

                        <div class="stat-info">

                            <span>Pendientes</span>

                            <h2>
                                {{ $pendientes[0]->total }}
                            </h2>

                            <small>
                                Por atender
                            </small>

                        </div>

                    </div>


                    <!-- EN PROCESO -->

                    <div class="stat-card">

                        <div class="stat-icon amarillo">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>

                        <div class="stat-info">

                            <span>En proceso</span>

                            <h2>
                                {{ $proceso[0]->total }}
                            </h2>

                            <small>
                                En atención
                            </small>

                        </div>

                    </div>


                    <!-- SOLUCIONADOS -->

                    <div class="stat-card">

                        <div class="stat-icon verde">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                        <div class="stat-info">

                            <span>Solucionados</span>

                            <h2>
                                {{ $solucionados[0]->total }}
                            </h2>

                            <small>
                                Completados
                            </small>

                        </div>

                    </div>

                </div>



                <!-- =========================
                     GRÁFICOS
                ========================== -->

                <div class="row">


                    <!-- GRÁFICO CIRCULAR -->

                    <div class="col-lg-6 col-md-12">

                        <div class="dashboard-card">

                            <div class="dashboard-header">

                                <div>

                                    <h4>
                                        Reportes por estado
                                    </h4>

                                    <p>
                                        Distribución actual de las incidencias
                                    </p>

                                </div>

                                <div class="dashboard-header-icon">
                                    <i class="bi bi-pie-chart-fill"></i>
                                </div>

                            </div>


                            <div class="grafico-container">

                                <canvas id="graficoEstado"></canvas>

                            </div>

                        </div>

                    </div>



                    <!-- CATEGORÍAS -->

                    <div class="col-lg-6 col-md-12">

                        <div class="dashboard-card">

                            <div class="dashboard-header">

                                <div>

                                    <h4>
                                        Reportes por categoría
                                    </h4>

                                    <p>
                                        Problemas registrados por área
                                    </p>

                                </div>

                                <div class="dashboard-header-icon">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>

                            </div>


                            <div class="categorias-list">

                                @forelse($categorias as $categoria)

                                                            <div class="categoria-item">


                                                                <div class="categoria-nombre">

                                                                    <span>
                                                                        {{ $categoria->categoria }}
                                                                    </span>

                                                                    <strong>
                                                                        {{ $categoria->total }}
                                                                    </strong>

                                                                </div>


                                                                <div class="categoria-barra">

                                                                    <div class="categoria-progreso" style="width:
                                                                            {{ $reportes[0]->total > 0
                                    ? ($categoria->total / $reportes[0]->total) * 100
                                    : 0
                                                                            }}%">
                                                                    </div>

                                                                </div>


                                                            </div>

                                @empty

                                    <div class="categoria-vacia">

                                        No hay reportes registrados.

                                    </div>

                                @endforelse

                            </div>

                        </div>

                    </div>

                </div>



                <!-- =========================
                     INFORMACIÓN INFERIOR
                ========================== -->

                <div class="dashboard-info">

                    <div class="dashboard-info-icon">

                        <i class="bi bi-info-circle-fill"></i>

                    </div>

                    <div>

                        <strong>
                            Información del sistema
                        </strong>

                        <p>
                            Las estadísticas se actualizan con los datos
                            registrados actualmente en el sistema.
                        </p>

                    </div>

                </div>


            </div>


            @include('plantillas.pie')

        </div>

    </div>



    <!-- =========================
         BOOTSTRAP ICONS
    ========================== -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- =========================
         CHART.JS
    ========================== -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <!-- =========================
         GRÁFICO
    ========================== -->

    <script>

        const ctx = document.getElementById('graficoEstado');

        new Chart(ctx, {

            type: 'doughnut',

            data: {

                labels: [
                    'Pendientes',
                    'En proceso',
                    'Solucionados'
                ],

                datasets: [{

                    data: [

                        {{ $pendientes[0]->total }},

                        {{ $proceso[0]->total }},

                        {{ $solucionados[0]->total }}

                    ],

                    backgroundColor: [

                        '#dc3545',
                        '#f0ad4e',
                        '#28c76f'

                    ],

                    borderColor: '#ffffff',

                    borderWidth: 3,

                    hoverOffset: 8

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '68%',

                plugins: {

                    legend: {

                        position: 'bottom',

                        labels: {

                            usePointStyle: true,

                            pointStyle: 'circle',

                            padding: 20,

                            font: {

                                size: 12

                            }

                        }

                    }

                }

            }

        });

    </script>


    <!-- =========================
         ESTILOS PERSONALIZADOS
    ========================== -->

    <link rel="stylesheet" href="{{ secure_asset('css/estilosextra.css') }}">


    @include('plantillas.scripts')

</body>

</html>
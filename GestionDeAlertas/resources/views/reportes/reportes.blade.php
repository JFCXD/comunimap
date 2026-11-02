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

            {{-- ENCABEZADO Y RESUMEN: SOLO PRESENTACIÓN --}}
            @php
                $cmTotalReportes = count($reportes);
                $cmPendientesReportes = collect($reportes)->filter(fn($r) => $r->estado === 'Pendiente')->count();
                $cmProcesoReportes = collect($reportes)->filter(fn($r) => $r->estado === 'En Proceso')->count();
                $cmSolucionadosReportes = collect($reportes)->filter(fn($r) => in_array($r->estado, ['Solucionado', 'Resuelto']))->count();
            @endphp
            <div class="page-breadcrumb">
                <div class="row align-items-center g-2">
                    <div class="col-md-7">
                        <div class="cm-page-heading">
                            <h4 class="page-title">{{ session('rol') == 'Administrador' ? 'Reportes del barrio' : 'Mis reportes' }}</h4>
                            <p class="cm-subtitle mb-0">{{ session('rol') == 'Administrador' ? 'Consulta y seguimiento de las incidencias registradas.' : 'Revisa el estado de tus incidencias registradas.' }}</p>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end">
                        @if(session('rol') == 'Administrador')
                            <a href="/reportes/pdf-todos" target="_blank" class="btn btn-primary">
                                <i class="fa fa-file-pdf me-1"></i> Exportar todos
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="container-fluid">

                <div class="cm-status-bar" aria-label="Resumen de reportes">
                    <div class="cm-status-pill"><i class="fas fa-clipboard-list text-primary" aria-hidden="true"></i><span><strong>{{ $cmTotalReportes }}</strong><br><small>Total</small></span></div>
                    <div class="cm-status-pill"><i class="fas fa-exclamation-circle text-danger" aria-hidden="true"></i><span><strong>{{ $cmPendientesReportes }}</strong><br><small>Pendientes</small></span></div>
                    <div class="cm-status-pill"><i class="fas fa-clock text-warning" aria-hidden="true"></i><span><strong>{{ $cmProcesoReportes }}</strong><br><small>En proceso</small></span></div>
                    <div class="cm-status-pill"><i class="fas fa-check-circle text-success" aria-hidden="true"></i><span><strong>{{ $cmSolucionadosReportes }}</strong><br><small>Solucionados</small></span></div>
                </div>

                {{-- MENSAJES DE RESULTADO --}}
                @if(session('success'))

                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>

                @endif

                @if($errors->any())

                    <div class="alert alert-danger alert-dismissible fade show" role="alert">

                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>

                    </div>

                @endif

                {{-- =========================================
                CASOS ESPECIALES: SOLO ADMINISTRADOR
                ========================================== --}}
                @if(session('rol') == 'Administrador')

                    @php
                        $secciones = [
                            [
                                'titulo' => 'Casos de atención urgente',
                                'descripcion' => 'Casos reportados por 5 o más vecinos diferentes.',
                                'prioridad' => 'Alta',
                                'color' => 'danger',
                                'icono' => 'fa-exclamation-triangle',
                                'casos' => $casosAlta
                            ],
                            [
                                'titulo' => 'Casos que requieren atención',
                                'descripcion' => 'Casos reportados por 3 o 4 vecinos diferentes.',
                                'prioridad' => 'Media',
                                'color' => 'warning',
                                'icono' => 'fa-exclamation-circle',
                                'casos' => $casosMedia
                            ],
                            [
                                'titulo' => 'Casos en seguimiento',
                                'descripcion' => 'Casos reportados por 2 vecinos diferentes.',
                                'prioridad' => 'Baja',
                                'color' => 'info',
                                'icono' => 'fa-clipboard-list',
                                'casos' => $casosBaja
                            ]
                        ];
                    @endphp

                    @foreach($secciones as $seccion)

                        {{-- No mostrar secciones vacías --}}
                        @if(count($seccion['casos']) > 0)

                            <div class="card mb-4">

                                <div class="card-body">

                                    <h4 class="card-title mb-3 text-{{ $seccion['color'] }}">
                                        <i class="fa {{ $seccion['icono'] }}"></i>

                                        {{ $seccion['titulo'] }}
                                    </h4>

                                    <p class="text-muted">
                                        {{ $seccion['descripcion'] }}
                                    </p>

                                    <div class="table-responsive">

                                        <table class="table table-hover align-middle">

                                            <thead class="table-light">

                                                <tr>
                                                    <th>Problema</th>
                                                    <th>Categoría</th>
                                                    <th>Ubicación</th>
                                                    <th>Vecinos</th>
                                                    <th>Prioridad</th>
                                                    <th>Acciones</th>
                                                </tr>

                                            </thead>

                                            <tbody>

                                                @foreach($seccion['casos'] as $caso)

                                                    <tr>

                                                        {{-- PROBLEMA --}}
                                                        <td>

                                                            <strong>
                                                                {{ $caso['titulo'] }}
                                                            </strong>

                                                            <small class="d-block text-muted">
                                                                {{ $caso['cantidadReportes'] }}
                                                                reportes agrupados
                                                            </small>

                                                        </td>

                                                        {{-- CATEGORÍA --}}
                                                        <td>
                                                            {{ $caso['categoria'] }}
                                                        </td>

                                                        {{-- UBICACIÓN --}}
                                                        <td>

                                                            @if($caso['direccion'])

                                                                {{ $caso['direccion'] }}

                                                            @else

                                                                <span class="text-muted">
                                                                    Ubicación en el mapa
                                                                </span>

                                                            @endif

                                                        </td>

                                                        {{-- VECINOS --}}
                                                        <td>

                                                            <strong>
                                                                {{ $caso['cantidadVecinos'] }}
                                                                vecinos
                                                            </strong>

                                                            <div class="mt-2">

                                                                {{--
                                                                Botón de consulta.

                                                                Siempre dice "Usuarios".

                                                                No permite seleccionar
                                                                ni modificar personas.
                                                                --}}

                                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#usuariosCaso{{ $caso['idReporte'] }}">
                                                                    <i class="fa fa-users"></i>
                                                                    Usuarios
                                                                </button>

                                                            </div>

                                                        </td>

                                                        {{-- PRIORIDAD --}}
                                                        <td>

                                                            <span
                                                                class="badge bg-{{ $seccion['color'] }} {{ in_array($seccion['color'], ['warning', 'info']) ? 'text-dark' : '' }}">
                                                                {{ $seccion['prioridad'] }}
                                                            </span>

                                                        </td>

                                                        {{-- CAMBIAR ESTADO DEL GRUPO --}}
                                                        <td>

                                                            <form method="POST"
                                                                action="{{ url('/reportes/casos/' . $caso['idReporte'] . '/estado') }}"
                                                                class="form-estado-grupo"
                                                                data-reportes="{{ $caso['cantidadReportes'] }}">

                                                                @csrf

                                                                <div class="d-flex flex-column gap-2" style="min-width: 170px;">

                                                                    <select name="estado" class="form-select form-select-sm" required>

                                                                        <option value="" selected disabled>
                                                                            Seleccionar estado
                                                                        </option>

                                                                        <option value="Pendiente">
                                                                            Pendiente
                                                                        </option>

                                                                        <option value="En Proceso">
                                                                            En Proceso
                                                                        </option>

                                                                        <option value="Solucionado">
                                                                            Solucionado
                                                                        </option>

                                                                    </select>

                                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                                        <i class="fa fa-save"></i>
                                                                        Guardar estado
                                                                    </button>

                                                                </div>

                                                            </form>

                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        @endif

                    @endforeach

                @endif

                {{-- =========================================
                LISTA GENERAL DE REPORTES
                ========================================== --}}
                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title mb-3">
                            {{ session('rol') == 'Administrador' ? 'Lista general de reportes' : 'Reportes registrados' }}
                        </h4>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle cm-report-table">

                                <thead class="table-light">

                                    <tr>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Foto</th>
                                        <th>Usuario</th>
                                        <th>Acciones</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @forelse($reportes as $reporte)

                                        <tr>

                                            {{-- TÍTULO --}}
                                            <td>

                                                <strong>
                                                    {{ $reporte->titulo }}
                                                </strong>

                                            </td>

                                            {{-- CATEGORÍA --}}
                                            <td>
                                                {{ $reporte->nombre }}
                                            </td>

                                            {{-- ESTADO --}}
                                            <td>

                                                @if($reporte->estado == 'Pendiente')

                                                    <span class="badge bg-danger">
                                                        Pendiente
                                                    </span>

                                                @elseif($reporte->estado == 'En Proceso')

                                                    <span class="badge bg-warning text-dark">
                                                        En Proceso
                                                    </span>

                                                @elseif(
                                                        $reporte->estado == 'Solucionado' ||
                                                        $reporte->estado == 'Resuelto'
                                                    )

                                                    <span class="badge bg-success">
                                                        Solucionado
                                                    </span>

                                                @else

                                                    <span class="badge bg-secondary">
                                                        {{ $reporte->estado }}
                                                    </span>

                                                @endif

                                            </td>

                                            {{-- FOTO --}}
                                            <td>

                                                @if($reporte->imagen)

                                                    <img src="{{ asset('storage/' . $reporte->imagen) }}" alt="Foto del reporte"
                                                        width="80" height="60" style="object-fit: cover; border-radius: 8px;">

                                                @else

                                                    <span class="text-muted">
                                                        Sin foto
                                                    </span>

                                                @endif

                                            </td>

                                            {{-- USUARIO --}}
                                            <td>
                                                {{ $reporte->usuario }}
                                            </td>

                                            {{-- ACCIONES GENERALES --}}
                                            <td>

                                                <a href="{{ url('/reportes/pdf/' . $reporte->idReporte) }}" target="_blank"
                                                    class="btn btn-primary btn-sm" title="Ver PDF">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                @if(session('rol') == 'Administrador')

                                                    <a href="{{ url('/reportes/editar/' . $reporte->idReporte) }}"
                                                        class="btn btn-warning btn-sm" title="Editar reporte">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="6" class="text-center text-muted py-4">
                                                No existen reportes registrados.
                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            @include('plantillas.pie')

            {{--
            VENTANAS DE USUARIOS

            Se crean FUERA de las tablas responsive.
            Así no se recortan los nombres.

            Solo aparecen para los administradores.
            --}}
            @if(session('rol') == 'Administrador')

                @foreach($secciones as $seccion)

                    @foreach($seccion['casos'] as $caso)

                        <div class="modal fade" id="usuariosCaso{{ $caso['idReporte'] }}" tabindex="-1" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title">

                                            <i class="fa fa-users me-2"></i>

                                            Usuarios del caso

                                        </h5>

                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>

                                    </div>

                                    <div class="modal-body">

                                        <p class="text-muted mb-3">

                                            <strong>
                                                {{ $caso['titulo'] }}
                                            </strong>

                                            <span class="d-block mt-1">

                                                {{ $caso['cantidadVecinos'] }}
                                                vecinos diferentes

                                            </span>

                                        </p>

                                        <div style="max-height: 300px; overflow-y: auto;">

                                            <ul class="list-group">

                                                @foreach($caso['usuarios'] as $usuario)

                                                    <li class="list-group-item d-flex align-items-center">

                                                        <i class="fa fa-user text-secondary me-3"></i>

                                                        <span>
                                                            {{ $usuario }}
                                                        </span>

                                                    </li>

                                                @endforeach

                                            </ul>

                                        </div>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Cerrar
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                @endforeach

            @endif

        </div>

    </div>

    @include('plantillas.scripts')

    <script>
        // Confirmación antes de cambiar el estado
        // de todos los reportes de un grupo.

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const formularios = document.querySelectorAll(
                    '.form-estado-grupo'
                );

                formularios.forEach(function (formulario) {

                    formulario.addEventListener(
                        'submit',
                        function (evento) {

                            const selector = formulario.querySelector(
                                'select[name="estado"]'
                            );

                            if (!selector || !selector.value) {
                                evento.preventDefault();
                                return;
                            }

                            const cantidad = formulario.getAttribute(
                                'data-reportes'
                            );

                            const estado = selector.value;

                            const mensaje =
                                '¿Deseas cambiar a "' +
                                estado +
                                '" los ' +
                                cantidad +
                                ' reportes de este caso?';

                            if (!window.confirm(mensaje)) {
                                evento.preventDefault();
                            }

                        }
                    );

                });

            }
        );
    </script>

</body>

</html>
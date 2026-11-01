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

            {{-- ENCABEZADO --}}
            <div class="page-breadcrumb bg-white">
                <div class="row align-items-center">

                    <div class="col-lg-4">
                        <h4 class="page-title">
                            REPORTES
                        </h4>
                    </div>

                    <div class="col-lg-8 text-end">

                        @if(session('rol') == 'Administrador')

                            <a href="/reportes/pdf-todos" target="_blank" class="btn btn-primary">
                                <i class="fa fa-file-pdf"></i>
                                Exportar todos
                            </a>

                        @endif

                    </div>
                </div>
            </div>

            <div class="container-fluid">

                {{-- ===========================================
                TABLAS ESPECIALES SOLO PARA ADMINISTRADOR
                ============================================ --}}
                @if(session('rol') == 'Administrador')

                    {{-- PRIORIDAD ALTA --}}
                    @if(count($casosAlta) > 0)

                        <div class="card mb-4">

                            <div class="card-body">

                                <h4 class="card-title mb-3 text-danger">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    Casos de atención urgente
                                </h4>

                                <p class="text-muted">
                                    Casos reportados por
                                    5 o más vecinos diferentes.
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

                                            @foreach($casosAlta as $caso)

                                                <tr>

                                                    <td>
                                                        <strong>
                                                            {{ $caso['titulo'] }}
                                                        </strong>

                                                        <small class="d-block text-muted">
                                                            {{ $caso['cantidadReportes'] }}
                                                            reportes agrupados
                                                        </small>
                                                    </td>

                                                    <td>
                                                        {{ $caso['categoria'] }}
                                                    </td>

                                                    <td>
                                                        @if($caso['direccion'])
                                                            {{ $caso['direccion'] }}
                                                        @else
                                                            <span class="text-muted">
                                                                Ubicación en el mapa
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <strong>
                                                            {{ $caso['cantidadVecinos'] }}
                                                        </strong>
                                                        vecinos

                                                        <small class="d-block text-muted">
                                                            {{ implode(', ', $caso['usuarios']) }}
                                                        </small>
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-danger">
                                                            Alta
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <a href="{{ url('/reportes/pdf/' . $caso['idReporte']) }}" target="_blank"
                                                            class="btn btn-primary btn-sm" title="Ver reporte representativo">
                                                            <i class="fa fa-eye"></i>
                                                            Ver
                                                        </a>
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>
                        </div>

                    @endif

                    {{-- PRIORIDAD MEDIA --}}
                    @if(count($casosMedia) > 0)

                        <div class="card mb-4">

                            <div class="card-body">

                                <h4 class="card-title mb-3 text-warning">
                                    <i class="fa fa-exclamation-circle"></i>
                                    Casos que requieren atención
                                </h4>

                                <p class="text-muted">
                                    Casos reportados por
                                    3 o 4 vecinos diferentes.
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

                                            @foreach($casosMedia as $caso)

                                                <tr>

                                                    <td>
                                                        <strong>
                                                            {{ $caso['titulo'] }}
                                                        </strong>

                                                        <small class="d-block text-muted">
                                                            {{ $caso['cantidadReportes'] }}
                                                            reportes agrupados
                                                        </small>
                                                    </td>

                                                    <td>
                                                        {{ $caso['categoria'] }}
                                                    </td>

                                                    <td>
                                                        @if($caso['direccion'])
                                                            {{ $caso['direccion'] }}
                                                        @else
                                                            <span class="text-muted">
                                                                Ubicación en el mapa
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <strong>
                                                            {{ $caso['cantidadVecinos'] }}
                                                        </strong>
                                                        vecinos

                                                        <small class="d-block text-muted">
                                                            {{ implode(', ', $caso['usuarios']) }}
                                                        </small>
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-warning text-dark">
                                                            Media
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <a href="{{ url('/reportes/pdf/' . $caso['idReporte']) }}" target="_blank"
                                                            class="btn btn-primary btn-sm" title="Ver reporte representativo">
                                                            <i class="fa fa-eye"></i>
                                                            Ver
                                                        </a>
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>
                        </div>

                    @endif

                    {{-- PRIORIDAD BAJA --}}
                    @if(count($casosBaja) > 0)

                        <div class="card mb-4">

                            <div class="card-body">

                                <h4 class="card-title mb-3 text-info">
                                    <i class="fa fa-clipboard-list"></i>
                                    Casos en seguimiento
                                </h4>

                                <p class="text-muted">
                                    Casos reportados por
                                    2 vecinos diferentes.
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

                                            @foreach($casosBaja as $caso)

                                                <tr>

                                                    <td>
                                                        <strong>
                                                            {{ $caso['titulo'] }}
                                                        </strong>

                                                        <small class="d-block text-muted">
                                                            {{ $caso['cantidadReportes'] }}
                                                            reportes agrupados
                                                        </small>
                                                    </td>

                                                    <td>
                                                        {{ $caso['categoria'] }}
                                                    </td>

                                                    <td>
                                                        @if($caso['direccion'])
                                                            {{ $caso['direccion'] }}
                                                        @else
                                                            <span class="text-muted">
                                                                Ubicación en el mapa
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <strong>
                                                            {{ $caso['cantidadVecinos'] }}
                                                        </strong>
                                                        vecinos

                                                        <small class="d-block text-muted">
                                                            {{ implode(', ', $caso['usuarios']) }}
                                                        </small>
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-info text-dark">
                                                            Baja
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <a href="{{ url('/reportes/pdf/' . $caso['idReporte']) }}" target="_blank"
                                                            class="btn btn-primary btn-sm" title="Ver reporte representativo">
                                                            <i class="fa fa-eye"></i>
                                                            Ver
                                                        </a>
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>
                        </div>

                    @endif

                @endif

                {{-- ===========================================
                TABLA GENERAL DE REPORTES
                ============================================ --}}
                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title mb-4">
                            Lista general de reportes
                        </h4>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

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

                                            <td>
                                                <strong>
                                                    {{ $reporte->titulo }}
                                                </strong>
                                            </td>

                                            <td>
                                                {{ $reporte->nombre }}
                                            </td>

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

                                            <td>
                                                {{ $reporte->usuario }}
                                            </td>

                                            <td>

                                                <a href="{{ url('/reportes/pdf/' . $reporte->idReporte) }}" target="_blank"
                                                    class="btn btn-primary btn-sm" title="Ver reporte en PDF">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                @if(session('rol') == 'Administrador')

                                                    <a href="{{ url('/reportes/editar/' . $reporte->idReporte) }}"
                                                        class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                @endif

                                                {{-- Conservamos el botón anterior.
                                                Revisar luego la protección
                                                de su ruta en el servidor. --}}
                                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modalEliminar" data-titulo="{{ $reporte->titulo }}"
                                                    data-url="{{ url('/reportes/eliminar/' . $reporte->idReporte) }}"
                                                    title="Eliminar">
                                                    <i class="fa fa-trash"></i>
                                                </button>

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

            {{-- MODAL DE ELIMINACIÓN --}}
            <div class="modal fade" id="modalEliminar" tabindex="-1">

                <div class="modal-dialog">

                    <div class="modal-content">

                        <div class="modal-header bg-secondary text-dark">

                            <h5 class="modal-title">
                                Confirmar eliminación
                            </h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>

                        </div>

                        <div class="modal-body">

                            <h5 id="nombreRegistro"></h5>

                            <p class="text-muted">
                                Esta acción no se puede deshacer.
                            </p>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>

                            <a id="btnEliminar" class="btn btn-danger">
                                Eliminar
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    @include('plantillas.scripts')

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const modal = document.getElementById(
                    'modalEliminar'
                );

                if (!modal) {
                    return;
                }

                modal.addEventListener(
                    'show.bs.modal',
                    function (evento) {

                        const boton = evento.relatedTarget;

                        if (!boton) {
                            return;
                        }

                        const titulo =
                            boton.getAttribute('data-titulo') || '';

                        const url =
                            boton.getAttribute('data-url') || '';

                        document.getElementById(
                            'nombreRegistro'
                        ).textContent =
                            '¿Eliminar "' + titulo + '"?';

                        document.getElementById(
                            'btnEliminar'
                        ).setAttribute('href', url);
                    }
                );
            }
        );
    </script>

</body>

</html>
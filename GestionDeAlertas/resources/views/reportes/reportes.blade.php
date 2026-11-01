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

            {{-- ENCABEZADO DE REPORTES --}}
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

                {{-- EXPLICACIÓN PARA EL ADMINISTRADOR --}}
                @if(session('rol') == 'Administrador')

                    <div class="alert alert-info">

                        <strong>
                            Coincidencias comunitarias:
                        </strong>

                        Se consideran reportes activos
                        de la misma categoría, realizados
                        por vecinos distintos y ubicados
                        a una distancia máxima de 5 metros.

                    </div>

                @endif

                {{-- TABLA DE REPORTES --}}
                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title mb-4">
                            Lista de Reportes
                        </h4>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle">

                                <thead class="table-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Categoría</th>
                                        <th>Estado</th>
                                        <th>Coincidencias</th>
                                        <th>Nivel</th>
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

                                            {{-- CANTIDAD DE COINCIDENCIAS --}}
                                            <td>

                                                @if($reporte->nivelCoincidencia === 'No aplica')

                                                    <span class="text-muted">
                                                        —
                                                    </span>

                                                @else

                                                    <strong>
                                                        {{ $reporte->cantidadCoincidencias }}
                                                    </strong>

                                                    <small class="text-muted d-block">

                                                        @if($reporte->cantidadCoincidencias === 1)
                                                            vecino
                                                        @else
                                                            vecinos
                                                        @endif

                                                    </small>

                                                @endif

                                            </td>

                                            {{-- NIVEL DE COINCIDENCIA --}}
                                            <td>

                                                @if($reporte->nivelCoincidencia === 'Muy reportado')

                                                    <span class="badge bg-danger">
                                                        Muy reportado
                                                    </span>

                                                @elseif($reporte->nivelCoincidencia === 'Frecuente')

                                                    <span class="badge bg-warning text-dark">
                                                        Frecuente
                                                    </span>

                                                @elseif($reporte->nivelCoincidencia === 'Recurrente')

                                                    <span class="badge bg-info text-dark">
                                                        Recurrente
                                                    </span>

                                                @elseif($reporte->nivelCoincidencia === 'Individual')

                                                    <span class="badge bg-secondary">
                                                        Individual
                                                    </span>

                                                @else

                                                    <span class="text-muted">
                                                        —
                                                    </span>

                                                @endif

                                            </td>

                                            {{-- FOTO --}}
                                            <td>

                                                @if($reporte->imagen)

                                                    <img src="{{ asset('storage/' . $reporte->imagen) }}"
                                                        alt="Evidencia del reporte" width="80" height="60"
                                                        style="object-fit: cover; border-radius: 8px;">

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

                                            {{-- ACCIONES --}}
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

                                                {{-- Mantener la eliminación existente.
                                                Se revisará su seguridad después. --}}
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
                                            <td colspan="8" class="text-center text-muted py-4">
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

            {{-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN --}}
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
        // Mostrar el nombre del reporte de forma segura.
        // No insertar el título usando innerHTML.

        document.addEventListener('DOMContentLoaded', function () {

            const modal = document.getElementById('modalEliminar');

            if (!modal) {
                return;
            }

            modal.addEventListener('show.bs.modal', function (evento) {

                const boton = evento.relatedTarget;

                if (!boton) {
                    return;
                }

                const nombre = boton.getAttribute('data-titulo') || '';
                const url = boton.getAttribute('data-url') || '';

                const texto = document.getElementById('nombreRegistro');
                const enlace = document.getElementById('btnEliminar');

                texto.textContent = '¿Eliminar "' + nombre + '"?';
                enlace.setAttribute('href', url);
            });

        });
    </script>

</body>

</html>
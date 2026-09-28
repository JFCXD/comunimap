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
                    <div class="row align-items-center">
                        <div class="col-lg-3">
                            <h4 class="page-title">REPORTES</h4>
                        </div>
                        <div class="col-lg-9 text-end">
                            @if(session('rol') == 'Administrador')
                                <a href="/reportes/pdf-todos" target="_blank" class="btn btn-primary">
                                    <i class="fa fa-file-pdf"></i>
                                    Exportar todos
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
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
                                        <th>Foto</th>
                                        <th>Usuario</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportes as $reporte)
                                        <tr>
                                            <td>{{ $reporte->titulo }}</td>
                                            <td>{{ $reporte->nombre }}</td>
                                            <td>
                                                @if($reporte->estado == "Pendiente")
                                                    <span class="badge bg-danger">
                                                        Pendiente
                                                    </span>
                                                @elseif($reporte->estado == "En Proceso")
                                                    <span class="badge bg-warning text-dark">
                                                        En Proceso
                                                    </span>
                                                @elseif($reporte->estado == "Resuelto")
                                                    <span class="badge bg-success">
                                                        Resuelto
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($reporte->imagen)
                                                    <img src="{{ asset('storage/' . $reporte->imagen) }}" alt="Foto del reporte"
                                                        width="80" height="60" style="object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <span class="text-muted">Sin foto</span>
                                                @endif
                                            </td>
                                            <td>{{ $reporte->usuario }}</td>
                                            <td>
                                                <a href="{{ url('/reportes/pdf/' . $reporte->idReporte) }}" target="_blank"
                                                    class="btn btn-primary btn-sm" title="Ver reporte en PDF">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if(session('rol') == 'Administrador')
                                                    <a href="/reportes/editar/{{ $reporte->idReporte }}"
                                                        class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif
                                                <button class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modalEliminar"
                                                    onclick="confirmarEliminar('{{ $reporte->titulo }}','/reportes/eliminar/{{ $reporte->idReporte }}')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @include('plantillas.pie')
            <!-- Modal Eliminar -->
            <div class="modal fade" id="modalEliminar" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary text-dark">
                            <h5 class="modal-title">
                                Confirmar eliminación
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                            </button>
                        </div>
                        <div class="modal-body">
                            <h5 id="nombreRegistro"></h5>
                            <p class="text-muted">
                                Esta acción no se puede deshacer.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">
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
        function confirmarEliminar(nombre, url) {
            document.getElementById("nombreRegistro").innerHTML =
                "¿Eliminar <b>" + nombre + "</b>?"
            document.getElementById("btnEliminar").href = url;
        }
    </script>
</body>

</html>
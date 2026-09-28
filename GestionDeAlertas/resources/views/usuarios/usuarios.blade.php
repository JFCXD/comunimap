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

                    <div class="col-lg-3">
                        <h4 class="page-title">USUARIOS</h4>
                    </div>

                    <div class="col-lg-9 text-end">
                        <a href="/registro" class="btn btn-success">
                            <i class="fa fa-user-plus"></i>
                            Nuevo Usuario
                        </a>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            Lista de Usuarios
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usuarios as $usuario)
                                        <tr>
                                            <td>{{ $usuario->idUsuario }}</td>
                                            <td>{{ $usuario->nombre }}</td>
                                            <td>{{ $usuario->email }}</td>
                                            <td>{{ $usuario->telefono }}</td>
                                            <td>{{ $usuario->rol }}</td>
                                            <td>
                                                @if($usuario->estado == "Activo")
                                                    <span class="badge bg-success">
                                                        Activo
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        Inactivo
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(session('rol') == 'Administrador')
                                                    <a href="/usuarios/editar/{{ $usuario->idUsuario }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif
                                                <button class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modalEliminar"
                                                    onclick="confirmarEliminar('{{ $usuario->nombre }}','/usuarios/eliminar/{{ $usuario->idUsuario }}')">
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
        </div>
    </div>

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
    <!-- Scripts para el modal -->
    @include('plantillas.scripts')
    <script>
        function confirmarEliminar(nombre, url) {
            document.getElementById("nombreRegistro").innerHTML =
                "¿Eliminar <b>" + nombre + "</b>?";
            document.getElementById("btnEliminar").href = url;
        }
    </script>
</body>

</html>
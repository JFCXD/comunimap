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
                        <h4 class="page-title">
                            USUARIOS
                        </h4>
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

                {{-- =====================================================
                MENSAJES
                ====================================================== --}}

                @if(session('success'))

                    <div class="alert alert-success">

                        {{ session('success') }}

                    </div>

                @endif

                {{-- =====================================================
                USUARIOS PENDIENTES
                ====================================================== --}}

                @php

                    $pendientes = collect($usuarios)
                        ->filter(function ($usuario) {

                            return strtolower(
                                trim($usuario->estado)
                            ) !== 'activo';

                        });

                @endphp

                @if($pendientes->count() > 0)

                    <div class="card border-warning mb-4">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <h4 class="card-title mb-0">

                                    <i class="fa fa-user-clock text-warning"></i>

                                    Usuarios pendientes de validación

                                </h4>

                                <span class="badge bg-warning text-dark">

                                    {{ $pendientes->count() }}

                                </span>

                            </div>

                            <div class="table-responsive">

                                <table class="table table-hover align-middle">

                                    <thead class="table-light">

                                        <tr>

                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Estado</th>
                                            <th>Acción</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        @foreach($pendientes as $usuario)

                                            <tr>

                                                <td>
                                                    {{ $usuario->nombre }}
                                                </td>

                                                <td>
                                                    {{ $usuario->email }}
                                                </td>

                                                <td>
                                                    {{ $usuario->telefono ?: '—' }}
                                                </td>

                                                <td>

                                                    <span class="badge bg-warning text-dark">
                                                        Pendiente
                                                    </span>

                                                </td>

                                                <td>

                                                    <a href="/usuarios/activar/{{ $usuario->idUsuario }}"
                                                        class="btn btn-success btn-sm">

                                                        <i class="fa fa-check"></i>

                                                        Activar

                                                    </a>

                                                    <a href="/usuarios/editar/{{ $usuario->idUsuario }}"
                                                        class="btn btn-warning btn-sm">

                                                        <i class="fa fa-eye"></i>

                                                        Revisar

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

                {{-- =====================================================
                LISTA GENERAL
                ====================================================== --}}

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

                                            <td>
                                                {{ $usuario->idUsuario }}
                                            </td>

                                            <td>
                                                {{ $usuario->nombre }}
                                            </td>

                                            <td>
                                                {{ $usuario->email }}
                                            </td>

                                            <td>
                                                {{ $usuario->telefono ?: '—' }}
                                            </td>

                                            <td>
                                                {{ $usuario->rol }}
                                            </td>

                                            <td>

                                                @if(
                                                        strtolower(trim($usuario->estado))
                                                        === 'activo'
                                                    )

                                                    <span class="badge bg-success">
                                                        Activo
                                                    </span>

                                                @else

                                                    <span class="badge bg-warning text-dark">
                                                        Pendiente
                                                    </span>

                                                @endif

                                            </td>

                                            <td>

                                                @if(
                                                        session('rol')
                                                        == 'Administrador'
                                                    )

                                                    @if(
                                                            strtolower(
                                                                trim($usuario->estado)
                                                            ) !== 'activo'
                                                        )

                                                        <a href="/usuarios/activar/{{ $usuario->idUsuario }}"
                                                            class="btn btn-success btn-sm" title="Activar usuario">

                                                            <i class="fa fa-check"></i>

                                                        </a>

                                                    @endif

                                                    <a href="/usuarios/editar/{{ $usuario->idUsuario }}"
                                                        class="btn btn-warning btn-sm" title="Editar usuario">

                                                        <i class="fa fa-edit"></i>

                                                    </a>

                                                @endif

                                                <button class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modalEliminar" onclick="confirmarEliminar(
                                                            '{{ $usuario->nombre }}',
                                                            '/usuarios/eliminar/{{ $usuario->idUsuario }}'
                                                        )" title="Eliminar usuario">

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

    {{-- =====================================================
    MODAL ELIMINAR
    ====================================================== --}}

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

    @include('plantillas.scripts')

    <script>

        function confirmarEliminar(nombre, url) {

            document.getElementById(
                "nombreRegistro"
            ).innerHTML =
                "¿Eliminar <b>" + nombre + "</b>?";

            document.getElementById(
                "btnEliminar"
            ).href = url;

        }

    </script>

</body>

</html>
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

            <div class="page-breadcrumb">
                <div class="row align-items-center g-2">
                    <div class="col-md-7">
                        <div class="cm-page-heading">
                            <h4 class="page-title">Usuarios</h4>
                            <p class="cm-subtitle mb-0">Gestiona las cuentas y revisa las solicitudes pendientes.</p>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end">
                        <a href="/registro" class="btn btn-success">
                            <i class="fa fa-user-plus me-1" aria-hidden="true"></i> Nuevo usuario
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

                {{-- Código mostrado una sola vez al administrador; nunca se guarda en claro. --}}
                @if(session('codigo_recuperacion'))
                    <div class="alert alert-warning" role="status">
                        <strong>Código temporal para el vecino #{{ session('usuario_recuperacion') }}:</strong>
                        <code class="d-block my-2" style="overflow-wrap:anywhere">{{ session('codigo_recuperacion') }}</code>
                        Entrega este código en privado, una vez comprobada su identidad. Caduca en 15 minutos.
                        No lo compartas en capturas, mensajes públicos ni lo guardes en documentos.
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

                    <div class="card border-warning mb-3">

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
                                                    {{ trim($usuario->nombre . ' ' . ($usuario->apellido ?? '')) }}
                                                    <small class="d-block text-muted">Edad:
                                                        {{ $usuario->fechaNacimiento ? \Carbon\Carbon::parse($usuario->fechaNacimiento)->age . ' años' : 'No registrada' }}
                                                    </small>
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

                                                    @if($usuario->rol === 'Usuario')
                                                        <form action="{{ route('usuarios.codigo-recuperacion', ['id' => $usuario->idUsuario]) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Ya verificaste presencialmente la identidad de este vecino? El código anterior quedará invalidado.');">
                                                            @csrf
                                                            <input type="hidden" name="identidad_verificada" value="1">
                                                            <button type="submit" class="btn btn-outline-primary btn-sm" title="Generar código de recuperación (solo tras verificar identidad)" aria-label="Código de recuperación para {{ trim($usuario->nombre . ' ' . ($usuario->apellido ?? '')) }}">
                                                                <i class="fa-solid fa-key" aria-hidden="true"></i>
                                                            </button>
                                                        </form>
                                                    @endif

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

                        <h4 class="card-title mb-3">
                            Lista de usuarios
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
                                                {{ trim($usuario->nombre . ' ' . ($usuario->apellido ?? '')) }}
                                                <small class="d-block text-muted">Edad:
                                                    {{ $usuario->fechaNacimiento ? \Carbon\Carbon::parse($usuario->fechaNacimiento)->age . ' años' : 'No registrada' }}
                                                </small>
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

                                                    {{-- Recuperación asistida para vecinos: no cambiar su contraseña desde el administrador. --}}
                                                    @if(strtolower(trim($usuario->rol)) === 'usuario')
                                                        <form action="{{ route('usuarios.codigo-recuperacion', ['id' => $usuario->idUsuario]) }}" method="POST" class="d-inline-block" onsubmit="return confirm('¿Verificaste personalmente la identidad de este vecino? El código anterior quedará invalidado.');">
                                                            @csrf
                                                            <input type="hidden" name="identidad_verificada" value="1">
                                                            <button type="submit" class="btn btn-outline-primary btn-sm" title="Generar código temporal de recuperación" aria-label="Generar código de recuperación para {{ trim($usuario->nombre . ' ' . ($usuario->apellido ?? '')) }}">
                                                                <i class="fa fa-key" aria-hidden="true"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <a href="/usuarios/editar/{{ $usuario->idUsuario }}"
                                                        class="btn btn-warning btn-sm" title="Editar usuario">

                                                        <i class="fa fa-edit"></i>

                                                    </a>

                                                @endif

                                                <button class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#modalEliminar" onclick="confirmarEliminar(
                                                            '{{ trim($usuario->nombre . ' ' . ($usuario->apellido ?? '')) }}',
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
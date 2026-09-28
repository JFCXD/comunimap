@include('plantillas.encabezado')

<body>
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>
<div id="main-wrapper"
        data-layout="vertical"
        data-navbarbg="skin5"
        data-sidebartype="full"
        data-sidebar-position="absolute"
        data-header-position="absolute"
        data-boxed-layout="full">
    @include('plantillas.encabezado2')
    @include('plantillas.menu')
    <div class="page-wrapper">
        <div class="page-breadcrumb bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="page-title">EDITAR REPORTE</h4>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        Editar Reporte
                    </h4>
                    <form action="/reportes/actualizar/{{ $reporte[0]->idReporte }}"
                            method="POST"
                            enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label>Título</label>
                            <input type="text"
                                    name="titulo"
                                    class="form-control"
                                    value="{{ $reporte[0]->titulo }}"
                                    required>
                        </div>
                        <div class="mb-3">
                            <label>Descripción</label>
                            <textarea
                                name="descripcion"
                                class="form-control"
                                rows="4"
                                required>{{ $reporte[0]->descripcion }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label>Dirección</label>
                            <input type="text"
                                    name="direccion"
                                    class="form-control"
                                    value="{{ $reporte[0]->direccion }}"
                                    required>
                        </div>
                        <div class="mb-3">
                            <label>Categoría</label>
                            <select name="idCategoria" class="form-control">
                                @foreach($categorias as $categoria)
                                    <option
                                        value="{{ $categoria->idCategoria }}"
                                        {{ $categoria->idCategoria == $reporte[0]->idCategoria ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="Pendiente"
                                    {{ $reporte[0]->estado=='Pendiente' ? 'selected':'' }}>
                                    Pendiente
                                </option>
                                <option value="En Proceso"
                                    {{ $reporte[0]->estado=='En Proceso' ? 'selected':'' }}>
                                    En Proceso
                                </option>
                                <option value="Resuelto"
                                    {{ $reporte[0]->estado=='Resuelto' ? 'selected':'' }}>
                                    Resuelto
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Foto actual</label>
                            <br>
                            @if($reporte[0]->imagen)
                                <img src="{{ asset('storage/'.$reporte[0]->imagen) }}"
                                    width="180"
                                    class="img-thumbnail mb-3">
                            @else
                                <p>Sin imagen</p>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label>Nueva foto (opcional)</label>
                            <input type="file"
                                   name="imagen"
                                   class="form-control">
                        </div>
                        <button class="btn btn-success">
                            Actualizar Reporte
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @include('plantillas.pie')
    </div>
</div>
@include('plantillas.scripts')
</body>
</html>
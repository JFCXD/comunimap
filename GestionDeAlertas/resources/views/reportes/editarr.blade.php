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
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>Revisa los datos del reporte:</strong>
                            <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    <form action="/reportes/actualizar/{{ $reporte[0]->idReporte }}"
                            method="POST"
                            enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label>Título</label>
                            <input type="text"
                                    name="titulo"
                                    class="form-control"
                                    value="{{ old('titulo', $reporte[0]->titulo) }}" minlength="3" maxlength="150"
                                    required>
                        </div>
                        <div class="mb-3">
                            <label>Descripción</label>
                            <textarea
                                name="descripcion"
                                class="form-control"
                                rows="4"
                                minlength="10" maxlength="3000"
                                required>{{ old('descripcion', $reporte[0]->descripcion) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label>Dirección</label>
                            <input type="text"
                                    name="direccion"
                                    class="form-control"
                                    value="{{ old('direccion', $reporte[0]->direccion) }}" minlength="3" maxlength="255"
                                    required>
                        </div>
                        <div class="mb-3">
                            <label>Categoría</label>
                            <select name="idCategoria" class="form-control">
                                @foreach($categorias as $categoria)
                                    <option
                                        value="{{ $categoria->idCategoria }}"
                                        {{ $categoria->idCategoria == old('idCategoria', $reporte[0]->idCategoria) ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option value="Pendiente"
                                    {{ old('estado', $reporte[0]->estado)=='Pendiente' ? 'selected':'' }}>
                                    Pendiente
                                </option>
                                <option value="En Proceso"
                                    {{ old('estado', $reporte[0]->estado)=='En Proceso' ? 'selected':'' }}>
                                    En Proceso
                                </option>
                                <option value="Solucionado"
                                    {{ in_array(old('estado', $reporte[0]->estado), ['Solucionado', 'Resuelto'], true) ? 'selected':'' }}>
                                    Solucionado
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
                                   class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <small class="text-muted">JPG, PNG o WEBP, hasta 5 MB.</small>
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
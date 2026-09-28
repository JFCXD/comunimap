@include('plantillas.encabezado')
@extends('plantillas.principal')

@section('contenido')

<div class="text-center mb-4">
    <h3 class="mt-3">
        Sistema de Gestión de Alertas Comunitarias
    </h3>
    <p class="text-muted">
        Editar Usuario
    </p>
</div>

<form action="/usuarios/actualizar/{{ $usuario[0]->idUsuario }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Nombre</label>
        <input type="text"
               name="nombre"
               class="form-control"
               value="{{ $usuario[0]->nombre }}"
               required>
    </div>

    <div class="mb-3">
        <label>Correo Electrónico</label>
        <input type="email"
               name="email"
               class="form-control"
               value="{{ $usuario[0]->email }}"
               required>
    </div>

    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text"
               name="telefono"
               class="form-control"
               value="{{ $usuario[0]->telefono }}">
    </div>

    <div class="mb-3">
        <label>Rol</label>

        <select name="rol" class="form-control">

            <option value="Administrador"
                {{ $usuario[0]->rol == 'Administrador' ? 'selected' : '' }}>
                Administrador
            </option>

            <option value="Usuario"
                {{ $usuario[0]->rol == 'Usuario' ? 'selected' : '' }}>
                Usuario
            </option>

        </select>

    </div>

    <div class="mb-3">
        <label>Estado</label>

        <select name="estado" class="form-control">

            <option value="Activo"
                {{ $usuario[0]->estado == 'Activo' ? 'selected' : '' }}>
                Activo
            </option>

            <option value="Inactivo"
                {{ $usuario[0]->estado == 'Inactivo' ? 'selected' : '' }}>
                Inactivo
            </option>

        </select>

    </div>

    <button type="submit" class="btn btn-primary w-100">
        Actualizar Usuario
    </button>

</form>

<div class="text-center mt-4">
    <a href="/usuarios">
        ← Volver a la lista de usuarios
    </a>
</div>

@endsection
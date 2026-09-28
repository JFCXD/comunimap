@include('plantillas.encabezado')
@extends('plantillas.principal')
@section('contenido')
    <div class="text-center mb-4">
        <h3 class="mt-3">
            Sistema de Gestión de Alertas Comunitarias
        </h3>
        <p class="text-muted">
            Registro de Usuario
        </p>
    </div>
    <form action="/registro" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Correo Electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control">
        </div>
        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirmar Contraseña</label>
            <input type="password" name="confirmar" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">
            Registrarse
        </button>
    </form>
    <div class="text-center mt-4">
        ¿Ya tienes una cuenta?
        <a href="/login">
            Iniciar sesión
        </a>
    </div>
@endsection
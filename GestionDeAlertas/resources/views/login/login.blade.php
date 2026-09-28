@include('plantillas.encabezado')
@extends('plantillas.principal')
@section('contenido')
    <div class="text-center mb-4">

        <h3 class="mt-3">
            Sistema de Gestión de Alertas Comunitarias
        </h3>
        <p class="text-muted">
            Iniciar Sesión
        </p>
    </div>
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form action="/login" method="POST">
        @csrf
        <div class="mb-3">
            <label>Correo Electrónico</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100" href="/inicio">
            Ingresar
        </button>
    </form>
    <div class="text-center mt-5">  
        ¿No tienes cuenta?
        <a href="/registro">
            Registrarse
        </a>
    </div>
    <!-- script del captcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection
<!-- //cambiado -->
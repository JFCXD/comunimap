@extends('plantillas.principal')

@section('contenido')
<h2 class="cm-auth-title">Recuperar contraseña</h2>
<p class="cm-auth-help">Escribe el correo de tu cuenta. Si existe, recibirás un enlace para crear una contraseña nueva.</p>

@if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
@if($errors->any())
<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('recuperacion.enviar') }}">
    @csrf
    <label for="correo-recuperar" class="form-label">Correo electrónico</label>
    <input id="correo-recuperar" class="form-control mb-3" type="email" name="email"
           value="{{ old('email') }}" maxlength="190" autocomplete="email" required>
    <button type="submit" class="btn btn-primary w-100">Enviar enlace de recuperación</button>
</form>
<div class="cm-auth-switch">
    ¿No tienes acceso al correo? <a href="{{ route('recuperacion.codigo') }}">Usar código del administrador</a>
</div>
<div class="cm-auth-switch"><a href="/login">Volver a iniciar sesión</a></div>
@endsection

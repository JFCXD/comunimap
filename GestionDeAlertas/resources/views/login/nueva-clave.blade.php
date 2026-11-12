@extends('plantillas.principal')

@section('contenido')
<h2 class="cm-auth-title">Crear nueva contraseña</h2>
<p class="cm-auth-help">Elige una contraseña nueva de al menos 6 caracteres. No compartas tu código de recuperación.</p>
@if($errors->any())
<div class="alert alert-danger" role="alert">
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif
<form action="{{ route('recuperacion.cambiar') }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $id }}">
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="via" value="{{ $via }}">
    <div class="mb-3">
        <label for="pass-nueva" class="form-label">Contraseña nueva</label>
        <input id="pass-nueva" type="password" name="password" class="form-control"
               minlength="6" maxlength="72" autocomplete="new-password" required>
    </div>
    <div class="mb-3">
        <label for="pass-confirmar" class="form-label">Confirmar contraseña</label>
        <input id="pass-confirmar" type="password" name="password_confirmation" class="form-control"
               minlength="6" maxlength="72" autocomplete="new-password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Guardar nueva contraseña</button>
</form>
<div class="cm-auth-switch"><a href="/login">Volver a iniciar sesión</a></div>
@endsection

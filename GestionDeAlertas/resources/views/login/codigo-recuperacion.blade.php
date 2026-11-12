@extends('plantillas.principal')

@section('contenido')
<h2 class="cm-auth-title">Recuperación asistida</h2>
<p class="cm-auth-help">Solicita un código al administrador de la OTB después de verificar tu identidad. El código caduca en 15 minutos.</p>
@if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('recuperacion.codigo.verificar') }}">
    @csrf
    <label for="correo-codigo" class="form-label">Correo de la cuenta</label>
    <input id="correo-codigo" type="email" name="email" class="form-control mb-3"
           value="{{ old('email') }}" required maxlength="190" autocomplete="email">
    <label for="clave-codigo" class="form-label">Código temporal</label>
    <input id="clave-codigo" type="text" name="codigo" class="form-control mb-3"
           maxlength="40" required autocomplete="off" spellcheck="false"
           placeholder="XXXXXX-XXXXXX-XXXXXX-XXXXXX">
    <button type="submit" class="btn btn-primary w-100">Continuar</button>
</form>
<div class="cm-auth-switch"><a href="/login">Volver al inicio de sesión</a></div>
@endsection

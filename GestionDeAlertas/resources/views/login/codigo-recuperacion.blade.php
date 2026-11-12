@extends('plantillas.principal')

@section('contenido')
<h2 class="cm-auth-title">Recuperación asistida</h2>
<p class="cm-auth-help">Si no puedes acceder a tu correo, pide al administrador de la OTB tu número de usuario y un código temporal, después de verificar tu identidad. El código caduca en 15 minutos.</p>
@if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('recuperacion.codigo.verificar') }}">
    @csrf
    <label for="id-vecino" class="form-label">Número de usuario (ID)</label>
    <input id="id-vecino" type="number" name="idUsuario" class="form-control mb-3"
           value="{{ old('idUsuario') }}" min="1" step="1" inputmode="numeric"
           placeholder="Ingresa tu ID aquí" required>
    <p class="text-muted small">El administrador te indicará tu ID cuando te entregue el código.</p>
    <label for="clave-codigo" class="form-label">Código temporal</label>
    <input id="clave-codigo" type="text" name="codigo" class="form-control mb-3"
           maxlength="40" required autocomplete="off" spellcheck="false"
           placeholder="XXXXXX-XXXXXX-XXXXXX-XXXXXX">
    <button type="submit" class="btn btn-primary w-100">Continuar</button>
</form>
<div class="cm-auth-switch"><a href="/login">Volver al inicio de sesión</a></div>
@endsection

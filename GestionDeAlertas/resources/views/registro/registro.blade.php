@extends('plantillas.principal')

@section('contenido')
    <h2 class="cm-auth-title">Crear una cuenta</h2>
    <p class="cm-auth-help">Regístrate para comunicar los problemas de tu barrio.</p>

    @if(session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="/registro" method="POST">
        @csrf
        <div class="mb-3">
            <label for="cm-reg-nombre" class="form-label">Nombre</label>
            <input id="cm-reg-nombre" type="text" name="nombre" class="form-control"
                value="{{ old('nombre') }}" autocomplete="name" required>
        </div>
        <div class="mb-3">
            <label for="cm-reg-apellido" class="form-label">Apellido</label>
            <input id="cm-reg-apellido" type="text" name="apellido" class="form-control"
                value="{{ old('apellido') }}" autocomplete="family-name" maxlength="150" required>
        </div>
        <div class="mb-3">
            <label for="cm-reg-nacimiento" class="form-label">Fecha de nacimiento</label>
            <input id="cm-reg-nacimiento" type="date" name="fechaNacimiento" class="form-control"
                value="{{ old('fechaNacimiento') }}" max="{{ date('Y-m-d') }}" required>
            <small class="form-text text-muted">La edad se calcula automáticamente a partir de esta fecha.</small>
        </div>
        <div class="mb-3">
            <label for="cm-reg-email" class="form-label">Correo electrónico</label>
            <input id="cm-reg-email" type="email" name="email" class="form-control"
                value="{{ old('email') }}" autocomplete="email" required>
        </div>
        <div class="mb-3">
            <label for="cm-reg-telefono" class="form-label">Teléfono (opcional)</label>
            <input id="cm-reg-telefono" type="tel" name="telefono" class="form-control"
                value="{{ old('telefono') }}" autocomplete="tel">
        </div>
        <div class="mb-3">
            <label for="cm-reg-pass" class="form-label">Contraseña</label>
            <input id="cm-reg-pass" type="password" name="password" class="form-control"
                autocomplete="new-password" required>
        </div>
        <div class="mb-3">
            <label for="cm-reg-confirmar" class="form-label">Confirmar contraseña</label>
            <input id="cm-reg-confirmar" type="password" name="confirmar" class="form-control"
                autocomplete="new-password" required>
        </div>
        <p class="cm-auth-note">
            <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
            Después de registrarte, el administrador debe aprobar tu cuenta para que puedas ingresar.
        </p>
        <button type="submit" class="btn btn-success w-100">
            <i class="fas fa-user-plus me-2" aria-hidden="true"></i>Registrarse
        </button>
    </form>
    <div class="cm-auth-switch">
        ¿Ya tienes una cuenta? <a href="/login">Inicia sesión</a>
    </div>
@endsection

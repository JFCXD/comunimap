@extends('plantillas.principal')

@section('contenido')
    <h2 class="cm-auth-title">Iniciar sesión</h2>
    <p class="cm-auth-help">Ingresa a ComuniMap con tu cuenta de vecino o administrador.</p>

    @if(session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="/login" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="cm-login-email">Correo electrónico</label>
            <input id="cm-login-email" type="email" name="email" class="form-control"
                value="{{ old('email') }}" placeholder="nombre@correo.com" autocomplete="username" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="cm-login-pass">Contraseña</label>
            <input id="cm-login-pass" type="password" name="password" class="form-control"
                placeholder="Tu contraseña" autocomplete="current-password" required>
        </div>
        <div class="mb-3 cm-auth-recaptcha">
            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
        </div>
        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>Ingresar
        </button>
    </form>
    <div class="cm-auth-switch">
        ¿Todavía no tienes una cuenta? <a href="/registro">Regístrate aquí</a>
    </div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

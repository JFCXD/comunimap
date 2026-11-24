@extends('publico.base')
@section('titulo', 'Información del proyecto')
@section('descripcion', 'Conoce ComuniMap, la plataforma de gestión comunitaria de la O.T.B. Universitario Alto en Cochabamba.')
@section('contenido')
<h1>Información de ComuniMap</h1>
<p class="intro">ComuniMap es una plataforma web y móvil orientada a mejorar la comunicación y el seguimiento de necesidades comunitarias en la O.T.B. Universitario Alto, Cochabamba, Bolivia.</p>

<h2>¿Para qué sirve?</h2>
<p>Facilita que los vecinos registren problemas de su comunidad y puedan consultar su seguimiento. La administración puede revisar los reportes, organizar casos coincidentes y actualizar su estado.</p>

<h2>Principales funciones</h2>
<ul>
    <li><strong>Reportes comunitarios:</strong> registro de incidencias con una descripción y, cuando corresponda, ubicación y fotografía.</li>
    <li><strong>Seguimiento:</strong> consulta de reportes y de su estado de atención.</li>
    <li><strong>Participación comunitaria:</strong> consulta de comunicaciones y actividades habilitadas para la comunidad.</li>
    <li><strong>Gestión administrativa:</strong> seguimiento de incidencias y elaboración de documentación de apoyo.</li>
    <li><strong>Recuperación de acceso:</strong> solicitud de un enlace temporal enviado al correo registrado en la cuenta.</li>
</ul>

<h2>Acceso a la plataforma</h2>
<p>Para utilizar las funciones que requieren una cuenta, accede al <a href="{{ url('/login') }}">inicio de sesión de ComuniMap</a>. El acceso y las acciones disponibles dependen del rol y del estado de la cuenta.</p>

<h2>Privacidad y contacto</h2>
<p>Consulta nuestra <a href="{{ route('publico.privacidad') }}">política de privacidad</a> para conocer qué información se utiliza en la plataforma. Para consultas relacionadas con ComuniMap puedes escribir a <a href="mailto:fercho657555@gmail.com">fercho657555@gmail.com</a>.</p>
@endsection

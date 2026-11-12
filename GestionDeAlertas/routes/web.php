<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InicioController;
use App\Http\Controllers\AlertasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RecuperacionClaveController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\DocumentoFormalController;
use App\Http\Controllers\ImagenComunidadController;

Route::get('/', function () {
    return redirect('/login');
});

// =====================================================
// REGISTRO
// =====================================================

Route::get(
    '/registro',
    [RegistroController::class, 'index']
);

Route::post(
    '/registro',
    [RegistroController::class, 'guardar']
);



// =====================================================
// LOGIN
// =====================================================

Route::get(
    '/login',
    [LoginController::class, 'index']
);

Route::post(
    '/login',
    [LoginController::class, 'ingresar']
);

Route::get(
    '/logout',
    [LoginController::class, 'salir']
);

// =====================================================
// RECUPERACIÓN DE ACCESO WEB (sin inicio de sesión)
// =====================================================
Route::get('/recuperar-clave', [RecuperacionClaveController::class, 'solicitarFormulario'])
    ->name('recuperacion.solicitar');
Route::post('/recuperar-clave', [RecuperacionClaveController::class, 'solicitar'])
    ->middleware('throttle:3,1')->name('recuperacion.enviar');
Route::get('/restablecer-clave/{id}/{token}', [RecuperacionClaveController::class, 'formulario'])
    ->whereNumber('id')->name('recuperacion.formulario');
Route::get('/recuperar-con-codigo', [RecuperacionClaveController::class, 'codigoFormulario'])
    ->name('recuperacion.codigo');
Route::post('/recuperar-con-codigo', [RecuperacionClaveController::class, 'codigoVerificar'])
    ->middleware('throttle:5,1')->name('recuperacion.codigo.verificar');
Route::post('/restablecer-clave', [RecuperacionClaveController::class, 'cambiar'])
    ->middleware('throttle:5,1')->name('recuperacion.cambiar');
// Recuperación asistida: solo administradores autenticados (validación también en controlador).
Route::post('/usuarios/{id}/codigo-recuperacion', [RecuperacionClaveController::class, 'emitirCodigo'])
    ->whereNumber('id')->middleware('throttle:5,1')->name('usuarios.codigo-recuperacion');

// =====================================================
// INICIO
// =====================================================

Route::get(
    '/inicio',
    [InicioController::class, 'index']
);

// =====================================================
// ALERTAS
// =====================================================

Route::get(
    '/alertas',
    [AlertasController::class, 'index']
);

Route::post(
    '/alertas',
    [AlertasController::class, 'guardar']
);

// =====================================================
// REPORTES
// =====================================================

Route::get(
    '/reportes',
    [ReportesController::class, 'index']
);

Route::get('/reportes/ver/{id}', [ReportesController::class, 'ver'])->whereNumber('id')->name('reportes.ver');

Route::get(
    '/reportes/pdf/{id}',
    [ReportesController::class, 'pdf']
);

Route::get(
    '/reportes/pdf-todos',
    [ReportesController::class, 'pdfTodos']
);

Route::get(
    '/reportes/editar/{id}',
    [ReportesController::class, 'editar']
);

Route::post(
    '/reportes/actualizar/{id}',
    [ReportesController::class, 'actualizar']
);

// CAMBIAR ESTADO DE UN REPORTE INDIVIDUAL (SOLO ADMINISTRADOR)
Route::post('/reportes/{id}/estado', [ReportesController::class, 'actualizarEstadoIndividual'])
    ->whereNumber('id')->name('reportes.estado.individual');

// =====================================================
// CAMBIAR ESTADO DE UN CASO AGRUPADO
// =====================================================

Route::post(
    '/reportes/casos/{id}/estado',
    [ReportesController::class, 'actualizarEstadoGrupo']
);

// =====================================================
// USUARIOS
// =====================================================

Route::get(
    '/usuarios',
    [UsuariosController::class, 'mostrarUsuarios']
);

Route::get(
    '/usuarios/editar/{id}',
    [UsuariosController::class, 'editar']
);

Route::post(
    '/usuarios/actualizar/{id}',
    [UsuariosController::class, 'actualizar']
);

Route::get(
    '/usuarios/activar/{id}',
    [UsuariosController::class, 'activar']
);

Route::get(
    '/usuarios/eliminar/{id}',
    [UsuariosController::class, 'eliminar']
);

// =====================================================
// ESTADÍSTICAS
// =====================================================

Route::get(
    '/estadisticas',
    [EstadisticasController::class, 'index']
);

Route::get('/estadisticas/pdf', [EstadisticasController::class, 'exportarPdf'])
    ->name('estadisticas.pdf');

// COMUNIDAD: lectura para usuarios activos; escritura y publicación solo administrador.
Route::get('/comunidad', [ComunidadController::class, 'index'])->name('comunidad.index');
Route::post('/comunidad', [ComunidadController::class, 'guardar'])->name('comunidad.guardar');
Route::post('/comunidad/{id}/actualizar', [ComunidadController::class, 'actualizar'])->whereNumber('id')->name('comunidad.actualizar');
Route::post('/comunidad/{id}/estado', [ComunidadController::class, 'estado'])->whereNumber('id')->name('comunidad.estado');
Route::post('/comunidad/{id}/votar', [ComunidadController::class, 'votar'])->whereNumber('id')->name('comunidad.votar');

// Imágenes de Comunidad: lectura pública y subida desde API con secreto de servicio.
Route::get('/comunidad/imagen/{nombre}', [ImagenComunidadController::class, 'mostrar'])
    ->name('comunidad.imagen');
Route::post('/comunidad/imagen/subir', [ImagenComunidadController::class, 'subirDesdeApi'])
    ->name('comunidad.imagen.subir');

// CARTAS FORMALES: únicamente administración web; no alteran reportes.
Route::get('/documentos/preparar', [DocumentoFormalController::class, 'preparar'])->name('documentos.preparar');
Route::post('/documentos/descargar', [DocumentoFormalController::class, 'descargar'])->name('documentos.descargar');
// Borrador opcional con IA: solo administración; límite de 5 solicitudes por minuto.
Route::post('/documentos/borrador-ia', [DocumentoFormalController::class, 'generarBorrador'])
    ->middleware('throttle:5,1')->name('documentos.borrador-ia');

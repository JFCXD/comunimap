<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Controllerinicio;

use App\Http\Controllers\InicioController;
use App\Http\Controllers\AlertasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('plantillas.principal');
});

// Route::get('/inicio', [Controllerinicio::class, 'metodoinicio']);

Route::get('/registro', [RegistroController::class, 'index']);
Route::post('/registro', [RegistroController::class, 'guardar']);

Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'ingresar']);
Route::get('/logout', [LoginController::class, 'salir']);



Route::get('/inicio', [InicioController::class, 'index']);


Route::get('/alertas', [AlertasController::class, 'index']);
Route::post('/alertas', [AlertasController::class, 'guardar']);


Route::get('/reportes', [ReportesController::class, 'index']);
Route::get('/reportes/pdf/{id}', [ReportesController::class, 'pdf']);
Route::get('/reportes/pdf-todos', [ReportesController::class, 'pdfTodos']);
Route::get('/reportes/editar/{id}', [ReportesController::class, 'editar']);
Route::post('/reportes/actualizar/{id}', [ReportesController::class, 'actualizar']);


Route::get('/usuarios', [UsuariosController::class, 'mostrarUsuarios']);
Route::get('/usuarios/editar/{id}', [UsuariosController::class, 'editar']);
Route::post('/usuarios/actualizar/{id}', [UsuariosController::class, 'actualizar']);
Route::get('/usuarios/eliminar/{id}', [UsuariosController::class, 'eliminar']);

Route::get('/estadisticas', [EstadisticasController::class, 'index']);



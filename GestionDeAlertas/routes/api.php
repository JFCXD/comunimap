<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportesController;

// Endpoint utilizado por la aplicación Android para subir evidencias
Route::post('/subir-imagen-reporte', [
    ReportesController::class,
    'subirImagen'
]);
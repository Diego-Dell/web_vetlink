<?php

use App\Http\Controllers\AccesoController;
use App\Http\Controllers\ConsultaController;
use Illuminate\Support\Facades\Route;

// 1. Público: landing e iniciar sesión (no hay registro en la web).
Route::get('/', [AccesoController::class, 'landing'])->name('landing');
Route::get('/login', [AccesoController::class, 'formulario'])->name('login');
Route::post('/login', [AccesoController::class, 'entrar'])->middleware('throttle:10,1')->name('login.entrar');
Route::post('/logout', [AccesoController::class, 'salir'])->name('logout');

// 2. Consulta de solo lectura para cuentas con sesión.
Route::middleware('vetlink.sesion')->prefix('app')->group(function () {
    Route::get('/', [ConsultaController::class, 'inicio'])->name('inicio');
    Route::get('/granja/{id}', [ConsultaController::class, 'elegirGranja'])->whereUuid('id')->name('granja');
    Route::get('/ganado', [ConsultaController::class, 'ganado'])->name('ganado');
    Route::get('/ganado/{id}', [ConsultaController::class, 'ficha'])->whereUuid('id')->name('ficha');
    Route::get('/reportes', [ConsultaController::class, 'reportes'])->name('reportes');
    Route::get('/perfil', [ConsultaController::class, 'perfil'])->name('perfil');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\PagosController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth:corporativos')->group(function () {
    Route::get('/mis-datos', [PerfilController::class, 'misDatos'])->name('mis-datos');
    Route::view('/historial-pagos', 'historial-pagos')->name('historial-pagos');
    Route::get('/contratos', [ContratoController::class, 'index'])->name('contratos.index');
    Route::get('/contratos/{cliente}/ver', [ContratoController::class, 'verViaApi'])->name('contratos.ver');
    Route::get('/notificar-pagos', [PagosController::class, 'form'])
        ->name('pagos.form');

    Route::get('/notificar-pagos', [PagosController::class, 'form'])->name('pagos.form');

Route::post('/pagos/subir', [PagosController::class, 'subirNomina'])
    ->name('pagos.procesarNomina'); // <-- solo POST

    Route::get('/pagos/subir', function () {
    return redirect()->route('pagos.form');   // tu vista /notificar-pagos
});

    Route::get('/pagos/guardar/{temp}/{clienteId}', [PagosController::class, 'guardarEnNas'])
        ->name('pagos.guardarEnNas');

    Route::get('/descargar-formato', [PagosController::class, 'descargarFormato'])
        ->name('pagos.descargarFormato');
});

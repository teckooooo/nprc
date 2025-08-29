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
    Route::view('/notificar-pagos', 'notificar-pagos')->name('notificar-pagos');
    Route::get('/descargar-formato', [PagosController::class, 'descargarFormato'])
        ->name('pagos.descargarFormato');
    Route::view('/soporte', 'soporte')->name('soporte');
});

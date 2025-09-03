<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\HistorialPagosController;
use App\Http\Controllers\CredencialesCorporativoController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/corporativos/credenciales', [CredencialesCorporativoController::class, 'create'])
    ->name('corporativos.credenciales.create'); // público o con guard si prefieres

Route::post('/corporativos/credenciales', [CredencialesCorporativoController::class, 'store'])
    ->name('corporativos.credenciales.store');

Route::middleware('auth:corporativos')->group(function () {
    Route::get('/mis-datos', [PerfilController::class,'misDatos'])->name('perfil.datos');
    Route::put('/mis-datos/usuario', [PerfilController::class,'updateUsuario'])->name('perfil.usuario.update');

    Route::get('/historial-pagos', [HistorialPagosController::class, 'index'])->name('historial-pagos');

    Route::get('/contratos', [ContratoController::class, 'index'])->name('contratos.index');
    Route::get('/contratos/{cliente}/ver', [ContratoController::class, 'verViaApi'])->name('contratos.ver');

    Route::get('/notificar-pagos', [PagosController::class, 'form'])->name('pagos.form');

    Route::post('/pagos/subir', [PagosController::class, 'subirNomina'])->name('pagos.procesarNomina');
    Route::get('/pagos/subir', fn() => redirect()->route('pagos.form'));
    Route::get('/pagos/guardar/{temp}/{clienteId}', [PagosController::class, 'guardarEnNas'])->name('pagos.guardarEnNas');
    Route::get('/descargar-formato', [PagosController::class, 'descargarFormato'])->name('pagos.descargarFormato');

    Route::view('/soporte', 'soporte')->name('soporte');
});

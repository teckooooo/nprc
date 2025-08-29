<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PerfilController;

// Mostrar el login (usa resources/views/index.blade.php)
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Procesar el login (ESTA es la ruta que tu form necesita)
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Cerrar sesión
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware('auth:corporativos')->group(function () {
    Route::get('/mis-datos', [PerfilController::class, 'misDatos'])->name('mis-datos');
    Route::view('/historial-pagos', 'historial-pagos')->name('historial-pagos');
    Route::view('/contratos', 'contratos')->name('contratos');
    Route::view('/notificar-pagos', 'notificar-pagos')->name('notificar-pagos');
    Route::view('/soporte', 'soporte')->name('soporte');
});

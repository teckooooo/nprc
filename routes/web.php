<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Mostrar el login (usa resources/views/index.blade.php)
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Procesar el login (ESTA es la ruta que tu form necesita)
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Cerrar sesión
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::view('/mis-datos', 'mis-datos')->name('mis-datos');
    Route::view('/contratos', 'contratos')->name('contratos');
    Route::view('/historial-pagos', 'historial-pagos')->name('historial-pagos');
    Route::view('/notificar-pagos', 'notificar-pagos')->name('notificar-pagos');
    Route::view('/soporte', 'soporte')->name('soporte');
});

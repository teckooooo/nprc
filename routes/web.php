<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'      => Route::has('login'),
        'canRegister'   => Route::has('register'),
        'laravelVersion'=> Application::VERSION,
        'phpVersion'    => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
| Grupo autenticado (una sola apertura y un solo cierre)
*/
Route::middleware(['auth'])->group(function () {

    // Health-check local de tu app
    Route::get('/ping-local', function () {
        return response()->json([
            'ok'   => true,
            'php'  => PHP_VERSION,
            'app'  => config('app.name'),
            'env'  => app()->environment(),
            'time' => now()->toDateTimeString(),
            'url'  => request()->fullUrl(),
        ]);
    })->name('ping.local');

    // Llama a api_mia.php?action=listSucursales en el servidor remoto
    Route::get('/sucursales', [ClienteController::class, 'sucursales'])
        ->name('sucursales.index');

    // Llama a api_mia.php?action=corpSucursal&sucursal={id}
    Route::get('/corporativos/{id}', [ClienteController::class, 'corporativos'])
        ->whereNumber('id')
        ->name('corporativos.index');

    // Perfil (como ya lo tenías)
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

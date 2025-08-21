<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/client/by-rut', [ClientsController::class, 'getClientByRut']);
Route::get('/client/last-five', [ClientsController::class, 'getLastFiveClients']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas con autenticación
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Solo la ruta para crear posts
    Route::post('/posts', [PostController::class, 'store']);
    
    // Y la ruta para listar los posts del usuario autenticado
    Route::get('/posts', [PostController::class, 'index']);
});
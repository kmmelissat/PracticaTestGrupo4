<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Ruta para crear un post
    Route::post('/posts', [PostController::class, 'store']);
    
    // Ruta para listar posts con opción de búsqueda
    Route::get('/posts', [PostController::class, 'index']);
});
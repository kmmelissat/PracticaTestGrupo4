<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Ruta para crear posts (sin autenticación para simplificar la prueba)
Route::post('/posts', [PostController::class, 'store']);

// Si prefieres mantener la autenticación, usa esto en su lugar:
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/posts', [PostController::class, 'store']);
// });
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TarefaController;

// Rotas de autenticação
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);
Route::get('/auth/verificar', [AuthController::class, 'verificar']);

// Rotas de tarefas
Route::get('/tarefas', [TarefaController::class, 'index']);
Route::post('/tarefas', [TarefaController::class, 'store']);
Route::put('/tarefas/{id}', [TarefaController::class, 'update']);
Route::delete('/tarefas/{id}', [TarefaController::class, 'destroy']);
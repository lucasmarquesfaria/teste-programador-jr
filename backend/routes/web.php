<?php

use App\Http\Controllers\TarefaController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tarefas', [TarefaController::class, 'index']);
    Route::post('/tarefas', [TarefaController::class, 'store']);
    Route::put('/tarefas/{id}', [TarefaController::class, 'update']);
    Route::delete('/tarefas/{id}', [TarefaController::class, 'destroy']);
});

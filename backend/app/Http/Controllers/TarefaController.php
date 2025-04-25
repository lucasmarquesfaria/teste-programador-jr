<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarefa;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TarefaController extends Controller
{
    private $key = 'your-secret-key'; // Deve ser a mesma chave do AuthController

    // Obter o ID do usuário atual a partir do token
    private function getUserIdFromToken(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }
        
        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            return $decoded->sub;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function index(Request $request)
    {
        $userId = $this->getUserIdFromToken($request);
        
        if (!$userId) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        
        $tarefas = Tarefa::where('usuario_id', $userId)
            ->select('id', 'usuario_id', 'titulo', 'descricao', 'status', 'created_at', 'updated_at')
            ->get();
        return response()->json($tarefas);
    }

    public function store(Request $request)
    {
        $userId = $this->getUserIdFromToken($request);
        
        if (!$userId) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        
        $request->validate([
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string',
        ]);

        $tarefa = Tarefa::create([
            'usuario_id' => $userId,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
        ]);

        return response()->json($tarefa, 201);
    }

    public function update(Request $request, $id)
    {
        $userId = $this->getUserIdFromToken($request);
        
        if (!$userId) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        
        $tarefa = Tarefa::where('usuario_id', $userId)->findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:200',
            'descricao' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $tarefa->update($request->only('titulo', 'descricao', 'status'));

        return response()->json($tarefa);
    }

    public function destroy(Request $request, $id)
    {
        $userId = $this->getUserIdFromToken($request);
        
        if (!$userId) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }
        
        $tarefa = Tarefa::where('usuario_id', $userId)->findOrFail($id);
        $tarefa->delete();

        return response()->json(['message' => 'Tarefa excluída com sucesso']);
    }
}

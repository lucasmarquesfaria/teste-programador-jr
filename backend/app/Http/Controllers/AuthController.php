<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Usuario;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    private $key = 'your-secret-key';

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $userFromDB = DB::table('usuarios')->where('email', $request->email)->first();
        
        if (!$userFromDB) {
            $userFromDB = DB::table('users')->where('email', $request->email)->first();
        }

        if (!$userFromDB) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        $passwordMatches = false;
        
        if (isset($userFromDB->senha) && $request->password === $userFromDB->senha) {
            $passwordMatches = true;
        } 
        else if (isset($userFromDB->password) && $request->password === $userFromDB->password) {
            $passwordMatches = true;
        }
        else if (isset($userFromDB->senha) && Hash::check($request->password, $userFromDB->senha)) {
            $passwordMatches = true;
        }
        else if (isset($userFromDB->password) && Hash::check($request->password, $userFromDB->password)) {
            $passwordMatches = true;
        }
        
        if (!$passwordMatches) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        $payload = [
            'iss' => "your-issuer",
            'sub' => $userFromDB->id,
            'iat' => time(),
            'exp' => time() + 60 * 60
        ];

        $token = JWT::encode($payload, $this->key, 'HS256');

        $name = $userFromDB->nome ?? $userFromDB->name ?? 'Usuário';

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $userFromDB->id,
                'name' => $name,
                'email' => $userFromDB->email,
            ]
        ]);
    }

    public function logout()
    {
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function me(Request $request)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['error' => 'Token não fornecido'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            
            $user = User::find($decoded->sub);
            
            if (!$user) {
                $user = Usuario::find($decoded->sub);
            }
            
            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], 404);
            }

            $name = $user->nome ?? $user->name ?? 'Usuário';

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $name,
                    'email' => $user->email,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }
    }

    public function verificar(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token não fornecido'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            
            $user = User::find($decoded->sub);
            
            if (!$user) {
                $user = Usuario::find($decoded->sub);
            }

            if (!$user) {
                return response()->json(['error' => 'Usuário não encontrado'], 404);
            }

            $name = $user->nome ?? $user->name ?? 'Usuário';

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $name,
                    'email' => $user->email,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token inválido ou expirado'], 401);
        }
    }
                    // funcão de registro de usuário
    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
        ]);

        try {
            $usuario = Usuario::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => $request->password,
            ]);

            $payload = [
                'iss' => "your-issuer",
                'sub' => $usuario->id,
                'iat' => time(),
                'exp' => time() + 60 * 60 * 24
            ];
            
            $token = JWT::encode($payload, $this->key, 'HS256');
            
            return response()->json([
                'message' => 'Usuário registrado com sucesso',
                'token' => $token,
                'user' => [
                    'id' => $usuario->id,
                    'name' => $usuario->nome,
                    'email' => $usuario->email,
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao registrar usuário: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao registrar usuário. Tente novamente.'], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\RegisterResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Enregistre un nouvel utilisateur .
     */
    public function register(RegisterRequest $request)
    {
    $data = $request->validated();

    $data['password'] =Hash::make($data['password']);


    $user = User::create($data);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message'      => 'Compte créé avec succès !',
        'user'         => new RegisterResource($user),
        // 'access_token' => $token,
        // 'token_type'   => 'Bearer',
    ], 201);
    }
    /** Authentifie un utilisateur et retourne un token d'authentification.
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $user = Auth::user();

        return response()->json([
            'user'         => new LoginResource($user),
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type'   => 'Bearer',
        ], 200);
    }
    /** Déconnecte l'utilisateur en révoquant tous ses tokens d'authentification.
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Utilisateur non authentifié'], 401);
            }

            // Révoquer tous les tokens de l'utilisateur
            $user->tokens()->delete();

            return response()->json(['message' => 'Déconnexion réussie']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /** Retourne les informations de l'utilisateur authentifié.
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}

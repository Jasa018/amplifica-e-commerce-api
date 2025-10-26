<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|min:3|max:255',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                Log::warning('API login failed', ['email' => $validated['email']]);
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }

            $token = $user->createToken('api-token')->plainTextToken;
            Log::info('API login successful', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('API login error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();
            Log::info('API logout', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Token revocado exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('API logout error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al cerrar sesión'], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            return response()->json($request->user());
        } catch (\Exception $e) {
            Log::error('API user info error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener información del usuario'], 500);
        }
    }
}

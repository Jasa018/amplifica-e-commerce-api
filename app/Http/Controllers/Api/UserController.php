<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::paginate(10);
            Log::info('Users retrieved successfully', ['count' => $users->count()]);
            return UserResource::collection($users);
        } catch (\Exception $e) {
            Log::error('Error retrieving users', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener usuarios'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Log::info('User created successfully', ['user_id' => $user->id, 'email' => $user->email]);
            return new UserResource($user);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('User creation validation failed', ['errors' => $e->errors()]);
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating user', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al crear usuario'], 500);
        }
    }

    public function show(User $user)
    {
        try {
            Log::info('User retrieved successfully', ['user_id' => $user->id]);
            return new UserResource($user);
        } catch (\Exception $e) {
            Log::error('Error retrieving user', ['user_id' => $user->id ?? null, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener usuario'], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'nullable|string|min:8',
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            Log::info('User updated successfully', ['user_id' => $user->id]);
            return new UserResource($user);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('User update validation failed', ['user_id' => $user->id, 'errors' => $e->errors()]);
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al actualizar usuario'], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                Log::warning('User attempted to delete themselves', ['user_id' => $user->id]);
                return response()->json(['error' => 'No puedes eliminar tu propio usuario'], 403);
            }

            $user->delete();
            Log::info('User deleted successfully', ['user_id' => $user->id]);
            return response()->json(['message' => 'Usuario eliminado exitosamente']);
        } catch (\Exception $e) {
            Log::error('Error deleting user', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al eliminar usuario'], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email', 'max:255'],
                'password' => ['required', 'min:3', 'max:255'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                Log::info('User logged in', ['email' => $credentials['email']]);
                return redirect()->intended('/dashboard');
            }

            Log::warning('Failed login attempt', ['email' => $credentials['email']]);
            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->onlyInput('email');
            
        } catch (\Exception $e) {
            Log::error('Login error', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'email' => 'Error en el sistema. Intente nuevamente.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            if ($user) {
                Log::info('User logged out', ['email' => $user->email]);
            }
            
            return redirect('/');
        } catch (\Exception $e) {
            Log::error('Logout error', ['error' => $e->getMessage()]);
            return redirect('/');
        }
    }
}

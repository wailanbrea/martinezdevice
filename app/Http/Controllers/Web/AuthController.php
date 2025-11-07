<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de login (PRIMERA PÁGINA)
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Login ULTRA simplificado - solo verificar credenciales
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ]);
        }
        
        // Verificar password
        $passwordOk = false;
        try {
            $passwordOk = \Hash::check($credentials['password'], $user->password);
        } catch (\Exception $e) {
            // Si Hash::check falla, usar password_verify directamente
            $passwordOk = password_verify($credentials['password'], $user->password);
        }
        
        if ($passwordOk) {
            // Guardar ID en sesión manualmente sin Auth::login
            session(['user_id' => $user->id]);
            session(['authenticated' => true]);
            
            return redirect()->route('dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}

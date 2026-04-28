<?php

namespace App\Http\Controllers;

// use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store()
    {
        $attributes = request()->validate([
            'username' => 'required|max:255|min:2|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:5|max:255',
            'terms' => 'required'
        ], [
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
        ]);
        
        // Usar transacción con lock para prevenir race conditions
        $user = DB::transaction(function () use ($attributes) {
            // Verificar nuevamente dentro de la transacción (double-check) con lock
            if (User::where('username', $attributes['username'])->lockForUpdate()->first()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'username' => ['Este nombre de usuario ya está en uso.']
                ]);
            }
            
            if (User::where('email', $attributes['email'])->lockForUpdate()->first()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['Este correo electrónico ya está registrado.']
                ]);
            }
            
            return User::create($attributes);
        });
        auth()->login($user);

        return redirect('/dashboard');
    }
}

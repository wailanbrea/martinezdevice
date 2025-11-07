<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard principal
     */
    public function index()
    {
        // Obtener usuario desde la sesión
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }
        
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }
        
        return view('dashboard', [
            'user' => $user
        ]);
    }
}

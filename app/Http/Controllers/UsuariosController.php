<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuariosController extends Controller
{
    /**
     * Verificar que el usuario sea administrador
     */
    private function checkAdmin()
    {
        if (!auth()->check() || !auth()->user()->hasRole('administrador')) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkAdmin();
        $query = User::with('roles');

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%");
            });
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(10);
        $roles = Rol::all();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAdmin();
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdmin();
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'firstname' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ], [
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'roles.required' => 'Debe asignar al menos un rol al usuario.',
        ]);

        DB::transaction(function () use ($validated) {
            // Crear usuario
            $usuario = User::create([
                'username' => $validated['username'],
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'] ?? '',
                'email' => $validated['email'],
                'password' => $validated['password'], // Se encripta automáticamente en el modelo
            ]);

            // Asignar roles
            $usuario->roles()->sync($validated['roles']);
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->checkAdmin();
        $usuario = User::with(['roles', 'reparaciones', 'reparacionesCompletadas'])
            ->findOrFail($id);
        
        $roles = Rol::all();

        return view('usuarios.show', compact('usuario', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkAdmin();
        $usuario = User::with('roles')->findOrFail($id);
        $roles = Rol::all();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkAdmin();
        $usuario = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'firstname' => 'required|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ], [
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'roles.required' => 'Debe asignar al menos un rol al usuario.',
        ]);

        DB::transaction(function () use ($usuario, $validated) {
            // Actualizar datos del usuario
            $usuario->update([
                'username' => $validated['username'],
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'] ?? '',
                'email' => $validated['email'],
            ]);

            // Actualizar contraseña si se proporcionó
            if (!empty($validated['password'])) {
                $usuario->password = $validated['password'];
                $usuario->save();
            }

            // Actualizar roles
            $usuario->roles()->sync($validated['roles']);
        });

        return redirect()->route('usuarios.show', $usuario)
            ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->checkAdmin();
        $usuario = User::findOrFail($id);

        // No permitir eliminar al usuario actual
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // Verificar si tiene reparaciones asignadas
        if ($usuario->reparaciones()->count() > 0 || $usuario->reparacionesCompletadas()->count() > 0) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No se puede eliminar el usuario porque tiene reparaciones asociadas.');
        }

        DB::transaction(function () use ($usuario) {
            // Eliminar relaciones con roles
            $usuario->roles()->detach();
            
            // Eliminar usuario
            $usuario->delete();
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }
}

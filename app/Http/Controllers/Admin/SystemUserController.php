<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Staff;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SystemUserController extends Controller
{
    public function index()
    {
        $usuarios = User::whereIn('role_id', [1, 2])->get();
        $roles = Role::whereIn('id_rol', [1, 2])->get();

        return view('admin.users.index', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users|ends_with:@ejegolfo.com',
            'password' => 'required|string|min:6',
            'role_id' => 'required|in:1,2',
        ], [
            'email.ends_with' => 'El correo debe terminar en @ejegolfo.com',
            'email.unique' => 'Este correo ya está registrado en el sistema.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        Staff::create([
            'rol_id' => $request->role_id,
            'nombre' => $request->name,
            'apellido_paterno' => ' ', 
            'correo' => $request->email,
            'contrasena' => Hash::make($request->password),
            'estado' => 'activo',
        ]);

        return back()->with('success', 'Acceso creado correctamente. El usuario ya puede iniciar sesión.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        $user->delete();
        return back()->with('success', 'Acceso revocado.');
    }
}
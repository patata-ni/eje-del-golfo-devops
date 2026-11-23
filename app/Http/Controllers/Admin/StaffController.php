<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Role; 
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $personal = Staff::with('role')
            ->when($search, function($query) use ($search) {
                return $query->where('nombre', 'LIKE', "%$search%")
                             ->orWhere('correo', 'LIKE', "%$search%");
            })->paginate(10);

        $roles = Role::all();
        return view('admin.staff.index', compact('personal', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido_paterno' => 'required',
            'correo' => 'required|email|unique:personal,correo',
            'contrasena' => 'required|min:6',
            'rol_id' => 'required|exists:roles,id_rol',
        ]);

        Staff::create([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
            'rol_id' => $request->rol_id,
            'estado' => 'activo',
        ]);

        return back()->with('success', 'Personal registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $persona = Staff::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required',
            'correo' => 'required|email|unique:personal,correo,' . $id . ',id_personal',
            'rol_id' => 'required|exists:roles,id_rol',
        ]);

        $persona->update([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'rol_id' => $request->rol_id,
        ]);

        return back()->with('success', 'Personal actualizado correctamente.');
    }

    public function destroy($id)
    {
        $persona = Staff::findOrFail($id);
        $persona->delete();
        return back()->with('success', 'Personal eliminado.');
    }
}
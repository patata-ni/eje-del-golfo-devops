<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Staff;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $choferes = Driver::with('personal')
            ->when($search, function($query) use ($search) {
                return $query->whereHas('personal', function($q) use ($search) {
                    $q->where('nombre', 'LIKE', "%$search%")
                      ->orWhere('apellido_paterno', 'LIKE', "%$search%")
                      ->orWhere('correo', 'LIKE', "%$search%");
                });
            })->paginate(10);

        $personalDisponible = Staff::where('rol_id', 4)->get();

        $ultimoChofer = Driver::orderBy('id_chofer', 'desc')->first();
        $nuevoNumero = 'EMP-001';
        if ($ultimoChofer) {
            $numero = (int) substr($ultimoChofer->numero_empleado, 4);
            $nuevoNumero = 'EMP-' . str_pad($numero + 1, 3, '0', STR_PAD_LEFT);
        }

        return view('admin.drivers.index', compact('choferes', 'personalDisponible', 'nuevoNumero'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'personal_id' => 'required|exists:personal,id_personal|unique:choferes,personal_id',
        ]);

        $ultimoChofer = Driver::orderBy('id_chofer', 'desc')->first();
        $nuevoNumero = 'EMP-001';
        
        if ($ultimoChofer) {
            $numero = (int) substr($ultimoChofer->numero_empleado, 4);
            $nuevoNumero = 'EMP-' . str_pad($numero + 1, 3, '0', STR_PAD_LEFT);
        }

        Driver::create([
            'personal_id' => $request->personal_id,
            'numero_empleado' => $nuevoNumero,
            'estado' => 'activo',
        ]);

        return back()->with('success', 'Chofer registrado correctamente con número: ' . $nuevoNumero);
    }

    public function update(Request $request, $id)
    {
        $chofer = Driver::findOrFail($id);
        
        $request->validate([
            'estado' => 'required|in:disponible,en_ruta,vacaciones,enfermo,de_baja',
        ]);

        $chofer->update([
            'estado' => $request->estado,
        ]);

        return back()->with('success', 'Estado del chofer actualizado correctamente.');
    }

    public function pdfReport()
    {
        $choferes = Driver::with('personal')->orderBy('estado', 'desc')->get();

        return view('admin.pdf.drivers', compact('choferes'));
    }

    public function destroy($id)
    {
        $chofer = Driver::findOrFail($id);
        $chofer->delete();
        return back()->with('success', 'Chofer eliminado del registro.');
    }
}
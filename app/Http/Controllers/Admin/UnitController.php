<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $unidades = Unit::when($search, function($query) use ($search) {
            return $query->where('numero_unidad', 'LIKE', "%$search%")
                         ->orWhere('placas', 'LIKE', "%$search%");
        })->paginate(10);

        return view('admin.units.index', compact('unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_unidad' => 'required|unique:unidades,numero_unidad',
            'placas' => 'required|unique:unidades,placas',
            'capacidad' => 'required|integer',
        ]);

        Unit::create([
            'numero_unidad' => $request->numero_unidad,
            'placas' => $request->placas,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'anio' => $request->anio,
            'capacidad' => $request->capacidad,
            'estado' => $request->estado,
        ]);

        return back()->with('success', 'Unidad registrada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $unidad = Unit::findOrFail($id);
        
        $request->validate([
            'numero_unidad' => 'required|unique:unidades,numero_unidad,' . $id . ',id_unidad',
            'placas' => 'required|unique:unidades,placas,' . $id . ',id_unidad',
        ]);

        $unidad->update([
            'numero_unidad' => $request->numero_unidad,
            'placas' => $request->placas,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'anio' => $request->anio,
            'capacidad' => $request->capacidad,
            'estado' => $request->estado,
        ]);

        return back()->with('success', 'Unidad actualizada correctamente.');
    }

    public function destroy($id)
    {
        $unidad = Unit::findOrFail($id);
        $unidad->delete();
        return back()->with('success', 'Unidad eliminada.');
    }
}
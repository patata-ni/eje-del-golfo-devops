<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Unit;
use App\Models\Departure;
use App\Models\Driver;

class RouteManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $rutas = Route::when($search, function ($query) use ($search) {
                return $query->where('origen', 'LIKE', "%$search%")
                             ->orWhere('destino', 'LIKE', "%$search%");
            })
            ->paginate(10);
        $unidades = Unit::all(); 
        $salidas = Departure::with(['unit', 'driver'])->get(); 

        return view('admin.routes.index', compact('rutas', 'unidades', 'salidas'));
    }

    public function update(Request $request, $id)
    {
        $ruta = Route::findOrFail($id);
        
        $request->validate([
            'origen' => 'required',
            'destino' => 'required',
            'precio' => 'required|numeric',
        ]);

        $ruta->update([
            'origen' => $request->origen,
            'destino' => $request->destino,
            'duracion_minutos' => $request->duracion_minutos,
            'precio' => $request->precio,
            'estado' => $request->estado,
        ]);

        return back()->with('success', 'Ruta actualizada correctamente.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'origen' => 'required',
            'destino' => 'required',
            'precio' => 'required|numeric',
        ]);

        Route::create([
            'origen' => $request->origen,
            'destino' => $request->destino,
            'duracion_minutos' => $request->duracion_minutos,
            'precio' => $request->precio,
            'estado' => 'activa',
        ]);

        return back()->with('success', 'Ruta creada correctamente.');
    }

    public function destroy($id)
    {
        $ruta = Route::findOrFail($id);
        $ruta->delete();
        return back()->with('success', 'Ruta eliminada.');
    }
}
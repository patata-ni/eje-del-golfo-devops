<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Route;

class ScheduleController extends Controller
{
    public function index()
    {
        $horarios = Schedule::with('route')->get();
        $rutas = Route::all();
        return view('admin.schedules.index', compact('horarios', 'rutas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruta_id' => 'required|exists:rutas,id_ruta',
            'hora_salida' => 'required',
        ]);

        Schedule::create([
            'ruta_id' => $request->ruta_id,
            'hora_salida' => $request->hora_salida,
            'estado' => 'activo',
        ]);

        return back()->with('success', 'Horario base creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $horario = Schedule::findOrFail($id);
        $request->validate([
            'hora_salida' => 'required',
            'estado' => 'required',
        ]);
        $horario->update([
            'hora_salida' => $request->hora_salida,
            'estado' => $request->estado,
        ]);
        return back()->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $horario = Schedule::findOrFail($id);
        $horario->delete();
        return back()->with('success', 'Horario eliminado.');
    }
}
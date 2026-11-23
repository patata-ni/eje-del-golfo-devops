<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departure;
use App\Models\Schedule;
use App\Models\Unit;
use App\Models\Driver;

class DepartureController extends Controller
{
    public function index()
    {
        $salidas = Departure::with(['schedule.route', 'unit', 'driver.personal'])->orderBy('fecha', 'desc')->get();
        $horarios = Schedule::with('route')->get();
        $unidades = Unit::where('estado', 'disponible')->get();
        $choferes = Driver::with('personal')->where('estado', 'activo')->get();

        return view('admin.departures.index', compact('salidas', 'horarios', 'unidades', 'choferes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id_horario',
            'fecha' => 'required|date',
            'unidad_id' => 'required|exists:unidades,id_unidad',
            'chofer_id' => 'required|exists:choferes,id_chofer',
        ]);

        Departure::create([
            'horario_id' => $request->horario_id,
            'fecha' => $request->fecha,
            'unidad_id' => $request->unidad_id,
            'chofer_id' => $request->chofer_id,
            'estado' => 'programada',
        ]);

        return back()->with('success', 'Salida programada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $salida = Departure::findOrFail($id);
        
        $request->validate([
            'unidad_id' => 'required|exists:unidades,id_unidad',
            'chofer_id' => 'required|exists:choferes,id_chofer',
            'estado' => 'required',
        ]);

        $salida->update([
            'unidad_id' => $request->unidad_id,
            'chofer_id' => $request->chofer_id,
            'estado' => $request->estado,
        ]);

        return back()->with('success', 'Salida actualizada correctamente.');
    }

    public function pdfReport()
    {
        $salidas = Departure::with(['schedule.route', 'unit', 'driver.personal'])
            ->orderBy('fecha', 'desc')->get();
        return view('admin.pdf.departures', compact('salidas'));
    }

    public function destroy($id)
    {
        $salida = Departure::findOrFail($id);
        $salida->delete();
        return back()->with('success', 'Salida cancelada.');
    }
}
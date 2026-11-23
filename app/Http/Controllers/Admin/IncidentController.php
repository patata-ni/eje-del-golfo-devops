<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index()
    {
        $incidencias = Incident::with('unit')->orderBy('fecha_reporte', 'desc')->get();
        $unidades = Unit::all();

        return view('admin.incidents.index', compact('incidencias', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unidad_id' => 'required|exists:unidades,id_unidad',
            'tipo' => 'required|string',
            'descripcion' => 'required|string',
            'prioridad' => 'required|in:baja,media,alta,critica',
        ]);

        Incident::create([
            'unidad_id' => $request->unidad_id,
            'personal_id' => Auth::user()->id,
            'tipo' => $request->tipo,
            'descripcion' => $request->descripcion,
            'prioridad' => $request->prioridad,
            'estado' => 'reportada',
        ]);

        if ($request->prioridad == 'critica') {
            $unidad = Unit::find($request->unidad_id);
            $unidad->update(['estado' => 'en_mantenimiento']);
        }

        return back()->with('success', 'Incidencia reportada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $incidencia = Incident::findOrFail($id);
        
        $request->validate([
            'estado' => 'required|in:reportada,en_revision,atendida,cerrada',
        ]);

        $incidencia->update([
            'estado' => $request->estado,
            'fecha_cierre' => $request->estado == 'cerrada' ? now() : null,
        ]);

        return back()->with('success', 'Incidencia actualizada correctamente.');
    }

    public function destroy($id)
    {
        $incidencia = Incident::findOrFail($id);
        $incidencia->delete();
        return back()->with('success', 'Incidencia eliminada.');
    }
}
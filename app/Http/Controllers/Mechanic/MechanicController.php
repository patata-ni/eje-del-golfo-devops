<?php
namespace App\Http\Controllers\Mechanic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Incident;
use App\Models\Unit;
use App\Models\Staff;
use App\Models\Piece;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MechanicController extends Controller
{
    public function index()
    {
        $reportesCount = Incident::whereIn('estado', ['reportada', 'en_revision'])->whereDoesntHave('maintenance')->count();
        $mantenimientosCount = Maintenance::whereIn('estado', ['pendiente', 'en_proceso'])->count();
        
        $mecanicos = Staff::where('rol_id', 2)->get();

        $piezasGrafica = Piece::with('inventory')->take(6)->get();
        $nombresPiezas = $piezasGrafica->pluck('nombre')->toArray();
        $stockActual = $piezasGrafica->map(fn($p) => $p->inventory->cantidad_actual ?? 0)->toArray();
        $stockMinimo = $piezasGrafica->pluck('stock_minimo')->toArray();
        
        return view('mechanic.dashboard', compact('reportesCount', 'mantenimientosCount', 'mecanicos', 'nombresPiezas', 'stockActual', 'stockMinimo'));
    }

    public function reports()
    {
        $reportesPendientes = Incident::with('unit')
            ->whereIn('estado', ['reportada', 'en_revision'])
            ->whereDoesntHave('maintenance')
            ->orderBy('prioridad', 'desc')
            ->get();

        $mecanicos = Staff::where('rol_id', 2)->get();

        return view('mechanic.reports.index', compact('reportesPendientes', 'mecanicos'));
    }

    public function maintenance()
    {
        $unidades = Unit::where('estado', '!=', 'fuera_de_servicio')->get();
        $mantenimientos = Maintenance::with(['unit'])
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $piezas = Piece::with('inventory')->whereHas('inventory', function($q) {
            $q->where('cantidad_actual', '>', 0);
        })->get();

        return view('mechanic.maintenance.index', compact('mantenimientos', 'unidades', 'piezas'));
    }

    public function inventory()
    {
        $piezas = Piece::with('inventory')->get();
        
        return view('mechanic.inventory.index', compact('piezas'));
    }

    public function createMaintenance(Request $request)
    {
        $request->validate([
            'unidad_id' => 'required|exists:unidades,id_unidad',
            'tipo' => 'required|in:preventivo,correctivo',
            'diagnostico' => 'required|string',
            'incidencia_id' => 'nullable|exists:incidencias,id_incidencia',
            'mecanico_asignado' => 'required|exists:personal,id_personal', // <-- Validamos
        ]);

        $mantenimiento = Maintenance::create([
            'unidad_id' => $request->unidad_id,
            'incidencia_id' => $request->incidencia_id,
            'tipo' => $request->tipo,
            'diagnostico' => $request->diagnostico,
            'fecha_inicio' => date('Y-m-d'),
            'estado' => 'pendiente',
        ]);

        DB::table('mantenimiento_mecanico')->insert([
            'mantenimiento_id' => $mantenimiento->id_mantenimiento,
            'mecanico_id' => $request->mecanico_asignado,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->incidencia_id) {
            Incident::where('id_incidencia', $request->incidencia_id)->update(['estado' => 'en_revision']);
        }
        Unit::where('id_unidad', $request->unidad_id)->update(['estado' => 'en_mantenimiento']);

        return back()->with('success', 'Orden de trabajo creada y asignada.');
    }

    public function update(Request $request, $id)
    {
        $mantenimiento = Maintenance::findOrFail($id);
        
        $request->validate([
            'estado' => 'required',
            'trabajo_realizado' => 'nullable|string',
        ]);

        $mantenimiento->update([
            'estado' => $request->estado,
            'trabajo_realizado' => $request->trabajo_realizado,
            'fecha_fin' => $request->estado == 'finalizado' ? date('Y-m-d') : null,
        ]);

        if ($request->filled('pieza_id') && $request->filled('cantidad_usada')) {
            $inventario = \App\Models\Inventory::where('pieza_id', $request->pieza_id)->first();
            if ($inventario && $inventario->cantidad_actual >= $request->cantidad_usada) {
                $inventario->decrement('cantidad_actual', $request->cantidad_usada);
                \App\Models\InventoryMovement::create([
                    'pieza_id' => $request->pieza_id,
                    'mantenimiento_id' => $mantenimiento->id_mantenimiento,
                    'personal_id' => Auth::id(),
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $request->cantidad_usada,
                    'fecha_movimiento' => now(),
                    'observaciones' => 'Uso en mantenimiento #' . $mantenimiento->id_mantenimiento,
                ]);
            }
        }

        if ($request->estado == 'finalizado') {
            Unit::where('id_unidad', $mantenimiento->unidad_id)->update(['estado' => 'disponible']);
            
                if ($mantenimiento->incidencia_id) {
                    Incident::where('id_incidencia', $mantenimiento->incidencia_id)->update(['estado' => 'cerrada']);
                }

                $admins = \App\Models\Staff::where('rol_id', 1)->get();
                foreach($admins as $admin) {
                    \App\Models\Notification::create([
                        'personal_id' => $admin->id_personal,
                        'tipo' => 'mantenimiento_finalizado',
                        'titulo' => 'Unidad Lista',
                        'mensaje' => 'La unidad ' . $mantenimiento->unit->numero_unidad . ' ha salido del taller y está disponible para asignarle ruta.',
                        'leida' => 0,
                        'mantenimiento_id' => $mantenimiento->id_mantenimiento,
                    ]);
                }
            }

        return back()->with('success', 'Avance guardado.');
    }
}
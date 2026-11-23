<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Departure;
use App\Models\Incident;
use App\Models\Piece;
use App\Models\Maintenance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $unidadesActivas = Unit::where('estado', 'disponible')->count();
        $totalUnidades = Unit::count();
        $salidasHoy = Departure::whereDate('fecha', date('Y-m-d'))->count();
        $incidenciasAbiertas = Incident::whereIn('estado', ['reportada', 'en_revision'])->count();
        
        $stockBajo = Piece::whereHas('inventory', function($q) {
            $q->whereColumn('cantidad_actual', '<=', 'piezas.stock_minimo');
        })->count();

        $ultimasIncidencias = Incident::with('unit')->orderBy('fecha_reporte', 'desc')->take(3)->get();

        $dias = [];
        $cantidadSalidas = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $dias[] = $fecha->format('d M');
            $cantidadSalidas[] = Departure::whereDate('fecha', $fecha)->count();
        }

        $piezasGrafica = Piece::with('inventory')->take(6)->get();
        $nombresPiezas = $piezasGrafica->pluck('nombre')->toArray();
        $stockActual = $piezasGrafica->map(fn($p) => $p->inventory->cantidad_actual ?? 0)->toArray();
        $stockMinimo = $piezasGrafica->pluck('stock_minimo')->toArray();

        return view('admin.dashboard', compact(
            'unidadesActivas', 'totalUnidades', 'salidasHoy', 'incidenciasAbiertas', 
            'stockBajo', 'ultimasIncidencias', 'dias', 'cantidadSalidas',
            'nombresPiezas', 'stockActual', 'stockMinimo'
        ));
    }

    public function pdfReport()
    {
        $mantenimientos = Maintenance::with(['unit'])
            ->where('estado', 'finalizado')
            ->orderBy('fecha_fin', 'desc')
            ->get();

        return view('admin.pdf.report', compact('mantenimientos'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departure;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function create($id)
    {
        $salida = Departure::with(['schedule.route', 'unit'])->findOrFail($id);
        $ruta = $salida->schedule->route;

        return response()->json([
            'id' => $salida->id_salida,
            'origen' => $ruta->origen,
            'destino' => $ruta->destino,
            'hora_salida' => \Carbon\Carbon::parse($salida->schedule->hora_salida)->format('H:i'),
            'fecha' => \Carbon\Carbon::parse($salida->fecha)->format('d/m/Y'),
            'precio' => $ruta->precio,
            'unidad' => $salida->unit->numero_unidad ?? 'Por asignar',
            'asientos' => $salida->unit->capacidad ?? 0,
        ]);
    }

    public function pdfTicket($id)
    {
        $salida = Departure::with(['schedule.route', 'unit'])->findOrFail($id);
        $ruta = $salida->schedule->route;
        
        $pasajero = Auth::user();

        return view('reservas.boleto', compact('salida', 'ruta', 'pasajero'));
    }
}
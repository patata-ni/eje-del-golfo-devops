<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Piece;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index()
    {
        $piezas = Piece::with('inventory')->get();
        return view('admin.inventory.index', compact('piezas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'stock_minimo' => 'required|integer',
            'cantidad_actual' => 'required|integer',
        ]);

        $pieza = Piece::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'unidad_medida' => 'pieza',
            'stock_minimo' => $request->stock_minimo,
            'estado' => 'activa',
        ]);

        Inventory::create([
            'pieza_id' => $pieza->id_pieza,
            'cantidad_actual' => $request->cantidad_actual,
            'ubicacion' => 'Almacén Central',
            'estado' => 'disponible',
        ]);

        return back()->with('success', 'Pieza registrada en inventario.');
    }

    public function update(Request $request, $id)
    {
        $inventario = Inventory::where('pieza_id', $id)->first();
        if ($inventario) {
            $inventario->update(['cantidad_actual' => $request->cantidad_actual]);
        }
        return back()->with('success', 'Stock actualizado.');
    }
}
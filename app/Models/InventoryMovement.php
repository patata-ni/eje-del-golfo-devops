<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'id_movimiento_inventario';

    protected $fillable = [
        'pieza_id',
        'personal_id',
        'mantenimiento_id',
        'tipo_movimiento',
        'cantidad',
        'fecha_movimiento',
        'observaciones',
    ];
}
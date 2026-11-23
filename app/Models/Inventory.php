<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model {
    protected $table = 'inventario';
    protected $primaryKey = 'id_inventario';
    protected $fillable = [
        'pieza_id', 
        'cantidad_actual', 
        'ubicacion', 
        'estado'
    ];

    public function piece() {
        return $this->belongsTo(Piece::class, 'pieza_id', 'id_pieza');
    }
}
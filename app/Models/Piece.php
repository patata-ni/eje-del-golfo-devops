<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Piece extends Model {
    protected $table = 'piezas';
    protected $primaryKey = 'id_pieza';
    protected $fillable = [
        'nombre', 
        'descripcion', 
        'unidad_medida', 
        'stock_minimo', 
        'estado'
    ];

    public function inventory() {
        return $this->hasOne(Inventory::class, 'pieza_id', 'id_pieza');
    }
}
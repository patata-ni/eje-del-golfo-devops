<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model {
    use HasFactory;

    protected $table = 'unidades';
    protected $primaryKey = 'id_unidad';

    protected $fillable = [
        'numero_unidad', 
        'placas', 
        'marca', 
        'modelo', 
        'anio', 
        'capacidad', 
        'estado', 
        'observaciones'
    ];
}
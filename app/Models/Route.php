<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model {
    use HasFactory;

    protected $table = 'rutas';
    protected $primaryKey = 'id_ruta';

    protected $fillable = [
        'origen', 
        'destino', 
        'duracion_minutos', 
        'duracion_maxima_minutos', 
        'precio', 
        'estado'
    ];
}
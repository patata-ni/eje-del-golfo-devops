<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model {
    
    protected $table = 'horarios';
    protected $primaryKey = 'id_horario';
    protected $fillable = ['ruta_id', 'hora_salida', 'estado'];
    
    public function route() {
        return $this->belongsTo(Route::class, 'ruta_id', 'id_ruta');
    }
}
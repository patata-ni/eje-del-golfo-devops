<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departure extends Model {
    use HasFactory;

    protected $table = 'salidas';
    protected $primaryKey = 'id_salida';

    protected $fillable = [
        'horario_id', 
        'fecha', 
        'unidad_id', 
        'chofer_id', 
        'hora_salida_real', 
        'estado', 
        'observaciones'
    ];

    public function unit() 
    {
        return $this->belongsTo(Unit::class, 'unidad_id', 'id_unidad');
    }

    public function driver() 
    {
        return $this->belongsTo(Driver::class, 'chofer_id', 'id_chofer');
    }
    
    public function schedule() 
    {
        return $this->belongsTo(Schedule::class, 'horario_id', 'id_horario');
    }
}
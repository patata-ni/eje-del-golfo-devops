<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model {

    protected $table = 'mantenimientos';
    protected $primaryKey = 'id_mantenimiento';
    protected $fillable = [
        'unidad_id', 
        'incidencia_id', 
        'tipo', 
        'fecha_inicio', 
        'fecha_fin', 
        'diagnostico', 
        'trabajo_realizado', 
        'observaciones', 
        'estado'
    ];
    
    public function unit() { 
        return $this->belongsTo(Unit::class, 'unidad_id', 'id_unidad');
    }

    public function incident() { 
        return $this->belongsTo(Incident::class, 'incidencia_id', 'id_incidencia'); 
    }
}
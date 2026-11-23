<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maintenance; 

class Incident extends Model {

    protected $table = 'incidencias';
    protected $primaryKey = 'id_incidencia';
    protected $fillable = [
        'unidad_id', 
        'personal_id', 
        'fecha_reporte', 
        'tipo', 
        'descripcion', 
        'prioridad', 
        'estado', 
        'fecha_cierre'
    ];
    
    public function unit() { 
        return $this->belongsTo(Unit::class, 'unidad_id', 'id_unidad'); 
    }

    public function maintenance() {
        return $this->hasOne(Maintenance::class, 'incidencia_id', 'id_incidencia');
    }
}
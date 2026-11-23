<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    protected $fillable = [
        'personal_id', 
        'tipo', 
        'titulo', 
        'mensaje', 
        'leida', 
        'mantenimiento_id'
    ];

    public function staff() {
        return $this->belongsTo(Staff::class, 'personal_id', 'id_personal');
    }
}
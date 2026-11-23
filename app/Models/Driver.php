<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model {
    use HasFactory;

    protected $table = 'choferes';
    protected $primaryKey = 'id_chofer';

    protected $fillable = [
        'personal_id', 
        'numero_empleado', 
        'estado'
    ];
    
    public function personal() {
        return $this->belongsTo(Staff::class, 'personal_id', 'id_personal');
    }
}
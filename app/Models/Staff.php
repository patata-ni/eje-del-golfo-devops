<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model {
    use HasFactory;
    protected $table = 'personal';
    protected $primaryKey = 'id_personal';

    protected $fillable = [
        'rol_id', 
        'nombre', 
        'apellido_paterno', 
        'apellido_materno', 
        'telefono', 
        'correo', 
        'contrasena', 
        'estado'
    ];

    public function role() {
        return $this->belongsTo(Role::class, 'rol_id', 'id_rol');
    }
}
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([1 => 'Administrador', 2 => 'Mecanico', 3 => 'Cliente'] as $id => $name) {
            DB::table('roles')->insertOrIgnore([
                'id_rol' => $id, 'nombre' => $name, 'descripcion' => $name,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        if (!config('demo.enabled')) {
            return;
        }

        foreach (['admin' => 1, 'mechanic' => 2, 'client' => 3] as $key => $role) {
            $email = config("demo.{$key}_email");
            $password = config("demo.{$key}_password");
            if (!$email || !$password || strlen($password) < 12) {
                throw new \RuntimeException('Las cuentas de revisión requieren correo y contraseña de al menos 12 caracteres en variables de entorno.');
            }
            User::firstOrCreate(['email' => $email], [
                'name' => "Revisión Eje del Golfo - {$key}",
                'password' => $password, 'role_id' => $role, 'email_verified_at' => now(),
            ]);
        }
    }
}

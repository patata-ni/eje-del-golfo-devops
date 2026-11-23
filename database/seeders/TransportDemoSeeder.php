<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransportDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (!config('demo.enabled') || DB::table('rutas')->exists()) {
            return;
        }
        $now = now();
        $unit = DB::table('unidades')->insertGetId([
            'numero_unidad' => 'EG-DEMO-01', 'placas' => 'DEMO-001',
            'marca' => 'Unidad de demostración', 'capacidad' => 40,
            'estado' => 'disponible', 'created_at' => $now, 'updated_at' => $now,
        ], 'id_unidad');
        foreach ([['Papantla', 50, 70], ['Gutiérrez Zamora', 100, 130], ['Martínez de la Torre', 180, 230]] as [$destination, $minutes, $price]) {
            $route = DB::table('rutas')->insertGetId([
                'origen' => 'Poza Rica', 'destino' => $destination,
                'duracion_minutos' => $minutes, 'duracion_maxima_minutos' => $minutes + 20,
                'precio' => $price, 'estado' => 'activa', 'created_at' => $now, 'updated_at' => $now,
            ], 'id_ruta');
            $schedule = DB::table('horarios')->insertGetId([
                'ruta_id' => $route, 'hora_salida' => '09:00:00',
                'dias_operacion' => 'Diario', 'estado' => 'activo', 'created_at' => $now, 'updated_at' => $now,
            ], 'id_horario');
            DB::table('salidas')->insert([
                'horario_id' => $schedule, 'fecha' => today(), 'unidad_id' => $unit,
                'estado' => 'programada', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}

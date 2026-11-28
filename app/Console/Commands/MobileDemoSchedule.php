<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MobileDemoSchedule extends Command
{
    protected $signature = 'mobile:demo-schedule {--days=14}';

    protected $description = 'Crear salidas identificadas como prueba para revisar la app móvil; nunca en producción.';

    public function handle(): int
    {
        if (! config('demo.enabled') || (! app()->environment(['local', 'testing']) && env('RAILWAY_ENVIRONMENT_NAME') !== 'pruebas')) {
            $this->error('Solo permitido con demostración habilitada en el entorno de pruebas.');

            return self::FAILURE;
        }
        $days = (int) $this->option('days');
        if ($days < 1 || $days > 31) {
            return self::INVALID;
        }
        DB::transaction(function () use ($days) {
            foreach (DB::table('rutas')->where('estado', 'activa')->orderBy('id_ruta')->get() as $route) {
                $now = now();
                $tag = 'MOVIL-PRUEBA-'.$route->id_ruta;
                $unit = DB::table('unidades')->where('numero_unidad', $tag)->value('id_unidad');
                if (! $unit) {
                    $unit = DB::table('unidades')->insertGetId(['numero_unidad' => $tag, 'placas' => $tag,
                        'modelo' => 'Unidad de prueba', 'capacidad' => 32, 'estado' => 'disponible', 'created_at' => $now, 'updated_at' => $now]);
                }
                $driver = DB::table('choferes')->where('numero_empleado', $tag)->value('id_chofer');
                if (! $driver) {
                    $staff = DB::table('personal')->insertGetId(['rol_id' => 3, 'nombre' => 'Conductor de prueba', 'apellido_paterno' => (string) $route->id_ruta,
                        'correo' => str_replace('-', '', $tag).'@example.test', 'contrasena' => bcrypt(bin2hex(random_bytes(24))),
                        'estado' => 'activo', 'created_at' => $now, 'updated_at' => $now]);
                    $driver = DB::table('choferes')->insertGetId(['personal_id' => $staff, 'numero_empleado' => $tag,
                        'estado' => 'disponible', 'created_at' => $now, 'updated_at' => $now]);
                }
                $hour = sprintf('%02d:00:00', 9 + ($route->id_ruta % 7));
                $schedule = DB::table('horarios')->where('ruta_id', $route->id_ruta)->where('hora_salida', $hour)->value('id_horario');
                if (! $schedule) {
                    $schedule = DB::table('horarios')->insertGetId(['ruta_id' => $route->id_ruta, 'hora_salida' => $hour,
                        'dias_operacion' => 'Prueba móvil', 'estado' => 'activo', 'created_at' => $now, 'updated_at' => $now]);
                }
                for ($day = 1; $day <= $days; $day++) {
                    $date = now('America/Mexico_City')->addDays($day)->toDateString();
                    if (! DB::table('salidas')->where('horario_id', $schedule)->where('fecha', $date)->where('unidad_id', $unit)->exists()) {
                        DB::table('salidas')->insert(['horario_id' => $schedule, 'fecha' => $date, 'unidad_id' => $unit, 'chofer_id' => $driver,
                            'estado' => 'programada', 'observaciones' => 'Salida ficticia para validar la aplicación móvil. No válida para viajar.',
                            'created_at' => $now, 'updated_at' => $now]);
                    }
                }
            }
        });
        $this->info('Salidas de prueba preparadas; no se modificaron reservas existentes.');

        return self::SUCCESS;
    }
}

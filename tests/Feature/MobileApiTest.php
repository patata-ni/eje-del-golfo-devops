<?php

namespace Tests\Feature;

use App\Models\Departure;
use App\Models\Driver;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    private const API = '/api/mobile/v1/';

    private function trip(): Departure
    {
        $route = Route::create(['origen' => 'Poza Rica', 'destino' => 'Papantla', 'precio' => 180, 'duracion_minutos' => 45, 'estado' => 'activa']);
        $schedule = Schedule::create(['ruta_id' => $route->id_ruta, 'hora_salida' => '10:00:00', 'estado' => 'activo']);
        $unit = Unit::create(['numero_unidad' => 'M1', 'placas' => 'TEST1', 'capacidad' => 40, 'estado' => 'disponible']);
        $personal = DB::table('personal')->insertGetId(['rol_id' => 3, 'nombre' => 'Privado', 'apellido_paterno' => 'Chofer', 'correo' => 'private@example.test', 'contrasena' => 'private']);
        $driver = Driver::create(['personal_id' => $personal, 'numero_empleado' => 'M1', 'estado' => 'disponible']);

        return Departure::create(['horario_id' => $schedule->id_horario, 'fecha' => now('America/Mexico_City')->addDay()->toDateString(),
            'unidad_id' => $unit->id_unidad, 'chofer_id' => $driver->id_chofer, 'estado' => 'programada']);
    }

    private function token(int $role = 3): string
    {
        return User::factory()->create(['role_id' => $role])->createToken('test')->plainTextToken;
    }

    public function test_registration_is_always_passenger_and_tokens_work_across_devices(): void
    {
        $this->postJson(self::API.'register', ['name' => 'Ana', 'email' => 'ANA@example.test', 'password' => 'pass-123456', 'role_id' => 1])
            ->assertCreated()->assertJsonPath('user.role', 'Pasajero')->assertJsonMissingPath('user.password');
        $this->assertDatabaseHas('users', ['email' => 'ana@example.test', 'role_id' => 3]);
        $token = $this->postJson(self::API.'login', ['email' => 'ana@example.test', 'password' => 'pass-123456'])->assertOk()->json('token');
        $this->withToken($token)->getJson(self::API.'me')->assertOk()->assertJsonPath('user.name', 'Ana');
        $this->withToken($token)->postJson(self::API.'logout')->assertNoContent();
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson(self::API.'me')->assertUnauthorized();
    }

    public function test_guests_staff_wrong_password_and_expired_tokens_are_rejected(): void
    {
        $this->getJson(self::API.'me')->assertUnauthorized();
        $admin = User::factory()->create(['role_id' => 1]);
        $this->postJson(self::API.'login', ['email' => $admin->email, 'password' => 'password'])->assertUnprocessable();
        $this->postJson(self::API.'login', ['email' => $admin->email, 'password' => 'wrong'])->assertUnprocessable();
        $this->withToken($admin->createToken('staff')->plainTextToken)->getJson(self::API.'me')->assertForbidden();
        $expired = User::factory()->create(['role_id' => 3])->createToken('expired', ['*'], now()->subMinute());
        $this->app['auth']->forgetGuards();
        $this->withToken($expired->plainTextToken)->getJson(self::API.'me')->assertUnauthorized();
    }

    public function test_seats_are_shared_and_retries_do_not_create_duplicate_reservations(): void
    {
        $d = $this->trip();
        $token = $this->token();
        $payload = ['service' => $d->id_salida, 'date' => $d->fecha, 'seat' => 35, 'requestId' => (string) Str::uuid()];
        $first = $this->withToken($token)->postJson(self::API.'reservations', $payload)->assertCreated()->assertJsonPath('ticket.price', fn ($price) => (float) $price === 180.0)->json('ticket.id');
        $this->withToken($token)->postJson(self::API.'reservations', $payload)->assertCreated()->assertJsonPath('ticket.id', $first);
        $this->withToken($token)->postJson(self::API.'reservations', array_replace($payload, ['seat' => 36]))->assertConflict();
        $this->assertDatabaseCount('mobile_reservations', 1);
        $this->withToken($token)->getJson(self::API.'me')->assertJsonCount(1, 'tickets');
        $this->app['auth']->forgetGuards();
        $other = $this->token();
        $this->withToken($other)->postJson(self::API.'reservations', array_replace($payload, ['requestId' => (string) Str::uuid()]))->assertConflict();
        $this->withToken($other)->getJson(self::API.'me')->assertJsonCount(0, 'tickets');
        $this->getJson(self::API.'catalog')->assertOk()->assertJsonCount(1, 'occupied')->assertJsonPath('occupied.0.seat', '35')
            ->assertJsonCount(0, 'users')->assertJsonCount(0, 'tickets')->assertDontSee('private@example.test');
    }

    public function test_server_rejects_wrong_dates_capacity_unavailable_units_and_past_departures(): void
    {
        $d = $this->trip();
        $this->withToken($this->token());
        $v = ['service' => $d->id_salida, 'date' => $d->fecha, 'seat' => 1, 'requestId' => (string) Str::uuid()];
        $this->postJson(self::API.'reservations', array_replace($v, ['seat' => 41]))->assertUnprocessable();
        $this->postJson(self::API.'reservations', array_replace($v, ['date' => '2099-01-01']))->assertConflict();
        $d->unit->update(['estado' => 'en_mantenimiento']);
        $this->postJson(self::API.'reservations', $v)->assertConflict();
        $this->getJson(self::API.'catalog')->assertJsonCount(0, 'services');
        $d->unit->update(['estado' => 'disponible']);
        $d->update(['fecha' => '2020-01-01']);
        $this->postJson(self::API.'reservations', array_replace($v, ['date' => '2020-01-01']))->assertConflict();
        $this->assertDatabaseCount('mobile_reservations', 0);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Departure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_log_in_to_view_departures(): void
    {
        $this->get('/rutas')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/mechanic')->assertRedirect('/login');
    }

    public function test_client_cannot_access_or_modify_administration(): void
    {
        $client = User::factory()->create(['role_id' => 3]);
        $this->actingAs($client)->get('/admin')->assertForbidden();
        $this->post('/admin/routes', ['origen' => 'Poza Rica', 'destino' => 'Papantla', 'precio' => 70])->assertForbidden();
        $this->get('/mechanic')->assertForbidden();
        $this->assertDatabaseCount('rutas', 0);
    }

    public function test_admin_can_create_update_and_remove_a_route(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => 1]));
        $this->post('/admin/routes', ['origen' => 'Poza Rica', 'destino' => 'Papantla', 'precio' => 70, 'duracion_minutos' => 50])->assertRedirect();
        $route = Route::firstOrFail();
        $this->get('/admin/routes')->assertOk()->assertSee('Gestión de Rutas');
        $this->put('/admin/routes/'.$route->id_ruta, ['origen' => 'Poza Rica', 'destino' => 'Papantla', 'precio' => 80, 'duracion_minutos' => 55, 'estado' => 'activa'])->assertRedirect();
        $this->assertDatabaseHas('rutas', ['id_ruta' => $route->id_ruta, 'precio' => 80]);
        $this->delete('/admin/routes/'.$route->id_ruta)->assertRedirect();
        $this->assertDatabaseMissing('rutas', ['id_ruta' => $route->id_ruta]);
    }

    public function test_route_creation_rejects_missing_fields(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => 1]))->post('/admin/routes', [])->assertSessionHasErrors(['origen','destino','precio']);
        $this->assertDatabaseCount('rutas', 0);
    }

    public function test_customer_can_filter_departures_and_view_the_ticket(): void
    {
        $route = Route::create(['origen' => 'Poza Rica', 'destino' => 'Papantla', 'duracion_minutos' => 50, 'precio' => 70, 'estado' => 'activa']);
        $schedule = Schedule::create(['ruta_id' => $route->id_ruta, 'hora_salida' => '09:00:00', 'estado' => 'activo']);
        $departure = Departure::create(['horario_id' => $schedule->id_horario, 'fecha' => today(), 'estado' => 'programada']);
        $this->actingAs(User::factory()->create(['role_id' => 3]));
        $this->get('/rutas?origen=Poza&destino=Papantla')->assertOk()->assertViewHas('corridas', fn ($rows) => $rows->count() === 1);
        $this->get('/rutas?destino=Inexistente')->assertOk()->assertViewHas('corridas', fn ($rows) => $rows->isEmpty());
        $this->get('/reservar/'.$departure->id_salida)->assertOk()->assertJsonPath('destino', 'Papantla');
        $this->get('/boleto/'.$departure->id_salida)->assertOk()->assertSee('Papantla');
        $this->get('/reservar/999999')->assertNotFound();
    }

    public function test_roles_redirect_to_their_respective_modules(): void
    {
        foreach ([1 => '/admin', 2 => '/mechanic', 3 => '/'] as $role => $url) {
            $this->post('/logout');
            $user = User::factory()->create(['role_id' => $role]);
            $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect($url);
        }
    }

    public function test_mechanic_can_view_maintenance_but_not_administration(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => 2]));
        $this->get('/mechanic')->assertOk();
        $this->get('/mechanic/inventory')->assertOk();
        $this->get('/admin')->assertForbidden();
    }
}

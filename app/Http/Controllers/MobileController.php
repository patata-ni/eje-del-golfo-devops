<?php

namespace App\Http\Controllers;

use App\Models\Departure;
use App\Models\Route;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileController extends Controller
{
    private function passenger(Request $request): User
    {
        $user = $request->user();
        abort_unless((int) $user->role_id === 3, 403, 'Esta aplicación es exclusiva para pasajeros.');

        return $user;
    }

    private function userRow(User $user): array
    {
        return ['id' => (string) $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => 'Pasajero'];
    }

    private function identity(User $user): array
    {
        return ['user' => $this->userRow($user), 'tickets' => DB::table('mobile_reservations')
            ->where('user_id', $user->id)->orderByDesc('created_at')->get()
            ->map(fn ($row) => json_decode($row->snapshot, true))->values()->all()];
    }

    private function access(User $user): array
    {
        return $this->identity($user) + ['token' => $user->createToken('eje-mobile', ['passenger'], now()->addDays(30))->plainTextToken];
    }

    public function register(Request $request)
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $values = $request->validate([
            'name' => 'required|string|max:255', 'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:128',
        ]);
        $user = User::create($values + ['role_id' => 3]);

        return response()->json($this->access($user), 201)->header('Cache-Control', 'no-store');
    }

    public function login(Request $request)
    {
        $values = $request->validate(['email' => 'required|email', 'password' => 'required|string|max:128']);
        $user = User::where('email', Str::lower(trim($values['email'])))->first();
        // Run the same expensive check for unknown emails to avoid a timing shortcut.
        $valid = Hash::check($values['password'], $user?->password ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');
        if (! $user || ! $valid || (int) $user->role_id !== 3) {
            throw ValidationException::withMessages(['email' => 'Revisa tu correo y contraseña de pasajero.']);
        }

        return response()->json($this->access($user))->header('Cache-Control', 'no-store');
    }

    public function me(Request $request)
    {
        return response()->json($this->identity($this->passenger($request)))->header('Cache-Control', 'no-store');
    }

    public function logout(Request $request)
    {
        $this->passenger($request)->currentAccessToken()?->delete();

        return response()->noContent();
    }

    private function departures()
    {
        return Departure::with(['schedule.route', 'unit', 'driver'])
            ->whereBetween('fecha', [now('America/Mexico_City')->toDateString(), now('America/Mexico_City')->addYear()->toDateString()])
            ->orderBy('fecha')->orderBy('id_salida');
    }

    private function usable(Departure $departure): bool
    {
        return $departure->estado === 'programada'
            && $departure->schedule?->estado === 'activo'
            && $departure->schedule?->route?->estado === 'activa'
            && $departure->unit?->estado === 'disponible'
            && $departure->driver?->estado === 'disponible'
            && ! DB::table('incidencias')->where('unidad_id', $departure->unidad_id)->whereNotIn('estado', ['cerrada', 'atendida'])->exists()
            && ! DB::table('mantenimientos')->where('unidad_id', $departure->unidad_id)->whereIn('estado', ['pendiente', 'en_proceso'])->exists();
    }

    public function catalog()
    {
        $routes = Route::where('estado', 'activa')->orderBy('id_ruta')->get()->map(function ($route) {
            $stops = DB::table('ruta_parada')->join('paradas', 'parada_id', '=', 'id_parada')
                ->where('ruta_id', $route->id_ruta)->orderBy('orden')->pluck('nombre')->all();

            return ['id' => (string) $route->id_ruta, 'origin' => $route->origen, 'destination' => $route->destino,
                'minutes' => (string) ($route->duracion_minutos ?? ''), 'distance' => '', 'type' => 'General',
                'price' => (string) $route->precio, 'status' => 'Activa',
                'stops' => implode("\n", array_unique([$route->origen, ...$stops, $route->destino]))];
        });
        $departures = $this->departures()->get()->filter(fn ($d) => $this->usable($d));
        $services = $departures->map(fn ($d) => ['id' => (string) $d->id_salida, 'route' => (string) $d->schedule->ruta_id,
            'unit' => (string) $d->unidad_id, 'driver' => (string) $d->chofer_id, 'date' => $d->fecha,
            'time' => substr($d->schedule->hora_salida, 0, 5), 'capacity' => (string) $d->unit->capacidad, 'status' => 'Programada'])->values();
        $units = $departures->unique('unidad_id')->map(fn ($d) => ['id' => (string) $d->unidad_id,
            'name' => 'Unidad '.$d->unit->numero_unidad, 'model' => (string) $d->unit->modelo, 'status' => 'Disponible'])->values();
        $drivers = $departures->unique('chofer_id')->map(fn ($d) => ['id' => (string) $d->chofer_id, 'status' => 'Activo'])->values();
        $occupied = DB::table('mobile_reservations')->whereIn('departure_id', $departures->pluck('id_salida'))
            ->get(['departure_id', 'seat'])->map(fn ($r) => ['service' => (string) $r->departure_id, 'seat' => (string) $r->seat])->values();

        return response()->json(['schema' => 1, 'alerts' => true, 'routes' => $routes, 'services' => $services, 'units' => $units,
            'drivers' => $drivers, 'occupied' => $occupied, 'users' => [], 'tickets' => [], 'audit' => [],
            'incidents' => [], 'maintenance' => [], 'inventory' => [], 'mode' => config('demo.enabled') ? 'test' : 'live', 'serverTime' => now()->toIso8601String()])
            ->header('Cache-Control', 'no-store');
    }

    public function reserve(Request $request)
    {
        $user = $this->passenger($request);
        $v = $request->validate(['service' => 'required|integer', 'date' => 'required|date_format:Y-m-d',
            'seat' => 'required|integer|min:1|max:200', 'requestId' => 'required|uuid']);
        $ticket = DB::transaction(function () use ($user, $v) {
            // Serialize retries per passenger, then serialize seat selection per departure.
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $previous = DB::table('mobile_reservations')->where('user_id', $user->id)->where('request_id', $v['requestId'])->first();
            if ($previous) {
                $ticket = json_decode($previous->snapshot, true);
                abort_unless($ticket['service'] === (string) $v['service'] && $ticket['date'] === $v['date'] && $ticket['seat'] === (string) $v['seat'], 409, 'La solicitud ya se utilizó para otra reserva.');

                return $ticket;
            }
            $d = Departure::with(['schedule.route', 'unit', 'driver'])->whereKey($v['service'])->lockForUpdate()->firstOrFail();
            abort_unless($this->usable($d), 409, 'Esta salida ya no está disponible.');
            $starts = CarbonImmutable::parse($d->fecha.' '.$d->schedule->hora_salida, 'America/Mexico_City');
            abort_unless($d->fecha === $v['date'] && $starts->isFuture(), 409, 'Esta salida ya no está disponible.');
            abort_unless($v['seat'] <= $d->unit->capacidad, 422, 'El asiento no existe en esta unidad.');
            // A locking read sees the commit from the preceding departure lock holder,
            // even if MySQL already opened a REPEATABLE READ snapshot above.
            abort_if(DB::table('mobile_reservations')->where('departure_id', $d->id_salida)->where('seat', $v['seat'])->lockForUpdate()->first() !== null, 409, 'Este asiento ya está reservado. Elige otro.');
            $r = $d->schedule->route;
            abort_unless($r->precio !== null, 409, 'Esta salida aún no tiene una tarifa disponible.');
            $ticket = ['id' => (string) Str::uuid(), 'user' => (string) $user->id, 'passenger' => $user->name,
                'service' => (string) $d->id_salida, 'route' => (string) $r->id_ruta, 'origin' => $r->origen, 'destination' => $r->destino,
                'date' => $d->fecha, 'time' => substr($d->schedule->hora_salida, 0, 5), 'seat' => (string) $v['seat'],
                'price' => (string) $r->precio, 'unit' => (string) $d->unidad_id,
                'created' => now()->toIso8601String(), 'status' => 'Reservado · pendiente de pago'];
            DB::table('mobile_reservations')->insert(['id' => $ticket['id'], 'user_id' => $user->id,
                'departure_id' => $d->id_salida, 'seat' => $v['seat'], 'request_id' => $v['requestId'],
                'snapshot' => json_encode($ticket), 'created_at' => now(), 'updated_at' => now()]);

            return $ticket;
        }, 3);

        return response()->json(['ticket' => $ticket], 201)->header('Cache-Control', 'no-store');
    }
}
